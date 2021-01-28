<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
namespace Firebear\ImportExport\Model\Import\Order\Shipment;

use Firebear\ImportExport\Model\ResourceModel\Order\Helper;
use Magento\ImportExport\Model\Import;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Shipping\Model\ShipmentNotifierFactory;
use Firebear\ImportExport\Model\Import\Order\AbstractAdapter;
use Firebear\ImportExport\Model\Import\Context;

/**
 * Order Track Import
 */
class Track extends AbstractAdapter
{
    /**
     * Entity Type Code
     *
     */
    const ENTITY_TYPE_CODE = 'order';

    /**
     * Entity Id Column Name
     *
     */
    const COLUMN_ENTITY_ID = 'entity_id';

    /**
     * Shipment Id Column Name
     *
     */
    const COLUMN_SHIPMENT_ID = 'parent_id';

    /**
     * Shipment Increment Id Column Name
     *
     */
    const COLUMN_SHIPMENT_INCREMENT_ID = 'shipment_increment_id';

    /**
     * Order Id Column Name
     *
     */
    const COLUMN_ORDER_ID = 'order_id';

    /**
     * Track Number Column Name
     *
     */
    const COLUMN_TRACK_NUMBER = 'track_number';

    /**
     * Error Codes
     */
    const ERROR_ENTITY_ID_IS_EMPTY = 'shipmentTrackIdIsEmpty';
    const ERROR_SHIPMENT_ID_IS_EMPTY = 'shipmentTrackParentIdIsEmpty';
    const ERROR_DUPLICATE_ENTITY_ID = 'duplicateShipmentTrackId';
    const ERROR_ORDER_ID_IS_EMPTY = 'shipmentTrackOrderIdIsEmpty';
    const ERROR_TRACK_NUMBER_IS_EMPTY = 'shipmentTrackNumberIsEmpty';
    const ERROR_SHIPMENT_INCREMENT_ID = 'shipmentTrackIncrementId';
    const ERROR_SHIPMENT_COUNT = 'shipmentItemCount';
    const ERROR_SHIPMENT_IS_EMPTY = 'shipmentItemIsEmpty';

    /**
     * Validation Failure Message Template Definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::ERROR_DUPLICATE_ENTITY_ID => 'Shipment Track entity_id is found more than once in the import file',
        self::ERROR_ENTITY_ID_IS_EMPTY => 'Shipment Track entity_id is empty',
        self::ERROR_SHIPMENT_ID_IS_EMPTY => 'Shipment Track parent_id is empty',
        self::ERROR_ORDER_ID_IS_EMPTY => 'Shipment Track order_id is empty',
        self::ERROR_TRACK_NUMBER_IS_EMPTY => 'Shipment Track track_number is empty',
        self::ERROR_SHIPMENT_INCREMENT_ID => 'Shipment with selected shipment:increment_id does not exist',
        self::ERROR_SHIPMENT_COUNT => 'Order has more than 1 Shipment. please specify Shipment ID.',
        self::ERROR_SHIPMENT_IS_EMPTY => 'Order does not have Shipment.',
    ];

    /**
     * Order Shipment Track Table Name
     *
     * @var string
     */
    protected $_mainTable = 'sales_shipment_track';

    /**
     * Resource Connection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Shipment Collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
     */
    protected $_shipmentCollection;

    /**
     * Shipment Collection Factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
     */
    protected $_shipmentCollectionFactory;

    /**
     * Shipment Notifier
     *
     * @var \Magento\Shipping\Model\ShipmentNotifier;
     */
    protected $_notifier;

    /**
     * Shipment Notifier Factory
     *
     * @var \Magento\Shipping\Model\ShipmentNotifierFactory;
     */
    protected $_notifierFactory;

    /**
     * Track constructor.
     * @param Context $context
     * @param Helper $resourceHelper
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param ShipmentNotifierFactory $notifierFactory
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Context $context,
        Helper $resourceHelper,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        ShipmentNotifierFactory $notifierFactory
    ) {
        $this->_shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->_notifierFactory = $notifierFactory;
        parent::__construct($context, $resourceHelper);
    }

    /**
     * Retrieve The Prepared Data
     *
     * @param array $rowData
     * @return array|bool
     */
    public function prepareRowData(array $rowData)
    {
        parent::prepareRowData($rowData);
        $rowData = $this->_extractField($rowData, 'shipment_track');
        return (count($rowData) && !$this->isEmptyRow($rowData))
            ? $rowData
            : false;
    }

    /**
     * Retrieve Entity Id If Entity Is Present In Database
     *
     * @param array $rowData
     * @return bool|int
     */
    protected function _getExistEntityId(array $rowData)
    {
        $bind = [
            ':order_id' => $this->_getOrderId($rowData),
            ':parent_id' => $this->_getShipmentId($rowData),
            ':track_number' => $rowData['track_number']
        ];
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->_connection->select();
        $select->from($this->getMainTable(), 'entity_id')
            ->where('parent_id = :parent_id')
            ->where('order_id = :order_id')
            ->where('track_number = :track_number');

        return $this->_connection->fetchOne($select, $bind);
    }

    /**
     * Prepare Data For Update
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareDataForUpdate(array $rowData)
    {
        $toCreate = [];
        $toUpdate = [];

        list($createdAt, $updatedAt) = $this->_prepareDateTime($rowData);
        /* auto generate shipment and order ids */
        if (!empty($rowData[self::COLUMN_SHIPMENT_INCREMENT_ID])) {
            $shipmentId = $this->_getExistShipmentId($rowData);
            if (empty($this->shipmentIdsMap[$shipmentId])) {
                $this->shipmentIdsMap[$shipmentId] = $shipmentId;
            }
            $rowData[self::COLUMN_SHIPMENT_ID] = $shipmentId;
            if (empty($rowData[self::COLUMN_ORDER_ID])) {
                $orderId = $this->_getOrderIdByShipment($rowData);
                if (empty($this->orderIdsMap[$orderId])) {
                    $this->orderIdsMap[$orderId] = $orderId;
                }
                $rowData[self::COLUMN_ORDER_ID] = $orderId;
            }
        } elseif (!empty($this->_currentOrderId) && empty($this->_currentShipmentId)) {
            $shipmentId = $this->_getShipmentIdByOrder();
            if (empty($this->shipmentIdsMap[$shipmentId])) {
                $this->shipmentIdsMap[$shipmentId] = $shipmentId;
            }
            $rowData[self::COLUMN_SHIPMENT_ID] = $shipmentId;
        }

        $newEntity = false;
        $entityId = $this->_getExistEntityId($rowData);
        if (!$entityId) {
            /* create new entity id */
            $newEntity = true;
            $entityId = $this->_getNextEntityId();
            $key = $rowData[self::COLUMN_ENTITY_ID] ?? $entityId;
            $this->_newEntities[$key] = $entityId;
        }
        $entityRow = [
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'entity_id' => $entityId,
            'parent_id' => $this->_getShipmentId($rowData),
            'order_id' => $this->_getOrderId($rowData)
        ];
        /* prepare data */
        $entityRow = $this->_prepareEntityRow($entityRow, $rowData);
        if ($newEntity) {
            $toCreate[] = $entityRow;
        } else {
            $toUpdate[] = $entityRow;
        }
        return [
            self::ENTITIES_TO_CREATE_KEY => $toCreate,
            self::ENTITIES_TO_UPDATE_KEY => $toUpdate
        ];
    }

    /**
     * Validate Row Data For Add/Update Behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        if (!empty($rowData[self::COLUMN_SHIPMENT_INCREMENT_ID])) {
            if (!$this->_getExistShipmentId($rowData)) {
                $this->addRowError(self::ERROR_SHIPMENT_INCREMENT_ID, $rowNumber);
            }
        } elseif (!empty($this->_currentOrderId) && empty($this->_currentShipmentId)) {
            $shipmentCount = $this->_getShipmentCount();
            if (1 < $shipmentCount) {
                $this->addRowError(self::ERROR_SHIPMENT_COUNT, $rowNumber);
            }
        } elseif ($this->_checkEntityIdKey($rowData, $rowNumber)) {
            if (empty($rowData[self::COLUMN_SHIPMENT_ID])) {
                $this->addRowError(self::ERROR_SHIPMENT_ID_IS_EMPTY, $rowNumber);
            }

            if (empty($rowData[self::COLUMN_ORDER_ID])) {
                $this->addRowError(self::ERROR_ORDER_ID_IS_EMPTY, $rowNumber);
            }
        }
        if (empty($rowData[self::COLUMN_TRACK_NUMBER])) {
            $this->addRowError(self::ERROR_TRACK_NUMBER_IS_EMPTY, $rowNumber);
        }
    }

    /**
     * Retrieve Shipment Id If Shipment Is Present In Database
     *
     * @param array $rowData
     * @return bool|int
     */
    protected function _getExistShipmentId(array $rowData)
    {
        $bind = [':increment_id' => $rowData[self::COLUMN_SHIPMENT_INCREMENT_ID]];
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->_connection->select();
        $select->from($this->getShipmentTable(), 'entity_id')
            ->where('increment_id = :increment_id');

        return $this->_connection->fetchOne($select, $bind);
    }

    /**
     * Retrieve Order Id If Shipment Is Present In Database
     *
     * @param array $rowData
     * @return bool|int
     */
    protected function _getOrderIdByShipment(array $rowData)
    {
        $bind = [':increment_id' => $rowData[self::COLUMN_SHIPMENT_INCREMENT_ID]];
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->_connection->select();
        $select->from($this->getShipmentTable(), 'order_id')
            ->where('increment_id = :increment_id');

        return $this->_connection->fetchOne($select, $bind);
    }

    /**
     * Retrieve count Shipment of order
     *
     * @return bool|int
     */
    protected function _getShipmentCount()
    {
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->_connection->select();
        $select->from(['s' => $this->getShipmentTable()], 'COUNT(*)')
            ->join(
                ['o' => $this->getOrderTable()],
                'o.entity_id = s.order_id',
                []
            )->where('o.increment_id = ?', $this->_currentOrderId);

        return $this->_connection->fetchOne($select);
    }

    /**
     * Retrieve Shipment Id by order
     *
     * @return bool|int
     */
    protected function _getShipmentIdByOrder()
    {
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->_connection->select();
        $select->from(['s' => $this->getShipmentTable()], 's.entity_id')
            ->join(
                ['o' => $this->getOrderTable()],
                'o.entity_id = s.order_id',
                []
            )->where('o.increment_id = ?', $this->_currentOrderId);

        return $this->_connection->fetchOne($select);
    }

    /**
     * Update And Insert Data In Entity Table
     *
     * @param array $toCreate Rows for insert
     * @param array $toUpdate Rows for update
     * @return $this
     */
    protected function _saveEntities(array $toCreate, array $toUpdate)
    {
        parent::_saveEntities($toCreate, $toUpdate);
        if ($this->_parameters['send_email']) {
            $this->_sendEmail(array_column($toCreate, 'parent_id'));
        }
        return $this;
    }

    /**
     * Send emails
     *
     * @param array $shipmentIds
     * @return $this
     */
    protected function _sendEmail(array $shipmentIds)
    {
        $this->addLogWriteln(__('Sending emails.'), $this->output, 'info');
        $collection = $this->getShipmentCollection()
            ->addFieldToFilter('entity_id', ['in' => array_unique($shipmentIds)]);

        try {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            foreach ($collection as $shipment) {
                $shipment->setEmailSent(true);
                $this->getNotifier()->notify($shipment);
            }
        } catch (\Exception $e) {
            $this->addLogWriteln(__('An error occurred while sending emails.'), $this->output, 'error');
            $this->_logger->critical($e);
        }
        return $this;
    }

    /**
     * Retrieve shipment collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getShipmentCollection()
    {
        if (!$this->_shipmentCollection) {
            $this->_shipmentCollection = $this->_shipmentCollectionFactory->create();
        }
        return $this->_shipmentCollection;
    }

    /**
     * Retrieve shipment notifier
     *
     * @return \Magento\Shipping\Model\ShipmentNotifier
     */
    public function getNotifier()
    {
        if (!$this->_notifier) {
            $this->_notifier = $this->_notifierFactory->create();
        }
        return $this->_notifier;
    }
}
