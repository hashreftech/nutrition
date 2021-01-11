<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_BookingSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\BookingSystem\Observer;

use Magento\Framework\Event\ObserverInterface;

class AfterProductSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @var \Webkul\BookingSystem\Model\InfoFactory
     */
    protected $_info;

    /**
     * @var \Webkul\BookingSystem\Model\SlotFactory
     */
    protected $_slot;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;
    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Webkul\BookingSystem\Helper\Data       $bookingHelper
     * @param \Webkul\BookingSystem\Model\InfoFactory $info
     * @param \Webkul\BookingSystem\Model\SlotFactory $slot
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\BookingSystem\Helper\Data $bookingHelper,
        \Webkul\BookingSystem\Model\InfoFactory $info,
        \Webkul\BookingSystem\Model\SlotFactory $slot,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
    ) {
        $this->_request = $request;
        $this->_bookingHelper = $bookingHelper;
        $this->_info = $info;
        $this->_slot = $slot;
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getProduct();
            $productId = $product->getId();
            $productType = $product->getTypeId();
            $infoModel = $this->_info->create();
            $slotModel = $this->_slot->create();
            $helper = $this->_bookingHelper;
            $data = $this->_request->getParams();
            if ($productType != "booking") {
                return;
            }

            $bookingType = $this->getBookingType($data, $productId);
            $isNew = false;
            $collection = $infoModel->getCollection()
                ->addFieldToFilter("product_id", $productId);
            if ($collection->getSize()<=0) {
                $isNew = true;
            }
            if ($collection->getSize()<=0 && $bookingType == 0) {
                return;
            } else {
                $previousBookingType = $helper->getBookingType($productId);
                if ($bookingType == 0 && $previousBookingType == 0) {
                    return;
                }
            }
            if ($bookingType == 0 || ($bookingType==2 && !array_key_exists("start", $data['info']))) {
                $helper->disableSlots($productId);
                $helper->deleteInfo($productId);
                return;
            }

            if (!array_key_exists("info", $data)) {
                return;
            }

            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $qty = 0;
            $result = $this->prepareOptions($data, $bookingType);
            if (empty($result)) {
                return;
            }

            $bookingInfo = $result['info'];
            $count = $result['total'];
            //Setting Booking Information
            $bookingInfo = $helper->getSerializedString($bookingInfo);
            if (!$isNew && $isNew==false) {
                $bookingData = $helper->getBookingInfo($productId);

                if ($this->canSaveSlots($bookingData, $bookingInfo, $qty)) {
                    $helper->disableSlots($productId);
                    $slotData = [
                        'product_id' => $productId,
                        'type' => $bookingType,
                        'status' => 1
                    ];

                    $slotModel->setData($slotData)->save();
                }
            } else {
                $slotData = [
                    'product_id' => $productId,
                    'type' => $bookingType,
                    'status' => 1
                ];
                $slotModel->setData($slotData)->save();
            }

            if (isset($data['product']['quantity_and_stock_status']['qty'])) {
                $qty = $data['product']['quantity_and_stock_status']['qty'];
            } elseif (isset($data['product']['stock_data']['qty'])) {
                $qty = $data['product']['stock_data']['qty'];
            }

            $infoData = [
                'product_id' => $productId,
                'type' => $bookingType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'info' => $bookingInfo,
                'qty' => $qty,
                'total_slots' => $count
            ];
            $collection = $infoModel->getCollection();
            $item = $helper->getDataByField($productId, 'product_id', $collection);
            if ($item) {
                $infoData['qty'] = $qty;
                $id = $item->getId();
                $infoModel->addData($infoData)->setId($id)->save();
            } else {
                $infoModel->setData($infoData)->save();
            }
            $this->_bookingHelper->checkBookingProduct($productId);
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterProductSave execute : ".$e->getMessage());
        }
    }

    /**
     * Get Booking Type
     *
     * @param array $data
     *
     * @return int
     */
    private function getBookingType($data, $productId)
    {
        $bookingType = 0;
        try {
            if (array_key_exists("booking_type", $data)) {
                $bookingType = $data['booking_type'];
            }
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterProductSave getBookingType : ".$e->getMessage());
        }

        return (int)$bookingType;
    }

    /**
     * Check Whether New Slots or Not
     *
     * @param array  $bookingData
     * @param string $bookingInfo
     * @param int    $qty
     *
     * @return bool
     */
    private function canSaveSlots($bookingData, $bookingInfo, $qty)
    {
        try {
            if ($bookingData['is_booking']) {
                $tempInfo = $this->_bookingHelper->getSerializedString($bookingData['info']);
                if (strcmp($bookingInfo, $tempInfo) !== 0) {
                    return true;
                } else {
                    if ($bookingData['qty'] != $qty) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterProductSave canSaveSlots : ".$e->getMessage());
        }

        return false;
    }

    /**
     * Prepare Options
     *
     * @param array $data
     * @param int   $bookingType
     *
     * @return array
     */
    private function prepareOptions($data, $bookingType)
    {
        $result = [];
        try {
            if ($bookingType == 1) {
                $result = $this->prepareManyBookingOptions($data);
            } elseif ($bookingType == 2) {
                $result = $this->prepareOneBookingOptions($data);
            }
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterProductSave prepareOptions : ".$e->getMessage());
        }
        return $result;
    }

    /**
     * Prepare Many Booking Options
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareManyBookingOptions($data)
    {
        try {
            $count = 1;
            $info = $data['info'];
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $slotTime = $data['time_slot'];
            $breakTime = $data['break_time'];
            $numOfDays = $this->getDateDifference($startDate, $endDate);
            for ($i = 0; $i <= $numOfDays; $i++) {
                $date = strtotime("+$i day", strtotime($startDate));
                $day = strtolower(date("l", $date));
                $status = $info[$day]['status'];
                if ($status == 1) {
                    $startHour = $info[$day]['start_hour'];
                    $startMinute = $info[$day]['start_minute'];
                    $endHour = $info[$day]['end_hour'];
                    $endMinute = $info[$day]['end_minute'];
                    $startCount = $startHour*60 + $startMinute;
                    $endCount = $endHour*60 + $endMinute;
                    $diff = $endCount - $startCount;
                    while ($diff >= $slotTime) {
                        $diff = $diff - ($breakTime + $slotTime);
                        $count++;
                    }
                }
            }

            unset($data['info']['start_hour']);
            unset($data['info']['start_minute']);
            unset($data['info']['end_hour']);
            unset($data['info']['end_minute']);
            $bookingInfo = $data['info'];
            $bookingInfo['time_slot'] = $slotTime;
            $bookingInfo['break_time'] = $breakTime;
            $result = [];
            $result['info'] = $bookingInfo;
            $result['start_date'] = $startDate;
            $result['end_date'] = $endDate;
            $result['total'] = $count-1;
            
            return $result;
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Observer_AfterProductSave prepareManyBookingOptions : ".$e->getMessage());
        }
    }

    /**
     * Prepare One Booking Options
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareOneBookingOptions($data)
    {
        try {
            $count = 1;
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $numOfDays = $this->getDateDifference($startDate, $endDate);
            $startData = $data['info']['start'];
            $endData = $data['info']['end'];
            $startDays = $startData['day'];
            for ($i = 0; $i <= $numOfDays; $i++) {
                $date = strtotime("+$i day", strtotime($startDate));
                $day = strtolower(date("l", $date));
                $date = strtolower(date("Y-m-d", $date));
                foreach ($startDays as $key => $startDay) {
                    if ($day == $startDay) {
                        $count++;
                    }
                }
            }

            $bookingInfo = ['start' => $startData, 'end' => $endData];
            $result = [];
            $result['info'] = $bookingInfo;
            $result['total'] = $count-1;
            return $result;
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Observer_AfterProductSave prepareOneBookingOptions : ".$e->getMessage());
        }
    }

    /**
     * Get Difference  of Dates
     *
     * @param string $firstDate
     * @param string $lastDate
     *
     * @return int
     */
    private function getDateDifference($firstDate, $lastDate)
    {
        $numOfDays = 0;
        try {
            $date1 = date_create($firstDate);
            $date2 = date_create($lastDate);
            $diff = date_diff($date1, $date2);
            $numOfDays = (int)$diff->format("%R%a");
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterProductSave getDateDifference : ".$e->getMessage());
        }

        return $numOfDays;
    }
}
