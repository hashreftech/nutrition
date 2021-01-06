<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/12/20
 * Time: 09:15 AM
 */
namespace OY\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;
use Magento\Customer\Controller\RegistryConstants;
use Webkul\BookingSystem\Model\ResourceModel\Booked\CollectionFactory;

class ReserveGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_hiddenInputField = 'reserve';


    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $_context;

    protected $_coreRegistry = null;

    /**
     * @var null
     */
    protected  $newProductIds = null;

    protected $currentBuildUrl;

    protected $_planFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\BookingSystem\Model\ResourceModel\Booked\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \OY\Plan\Model\PlanFactory $planFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->jsonEncoder = $jsonEncoder;
        $this->currentBuildUrl = $context->getUrlBuilder();
        $this->_planFactory = $planFactory;
        $this->_context = $context;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $backendHelper, $data);

    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('reserve_listing');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);

    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _addColumnFilterToCollection($column)
    {

        parent::_addColumnFilterToCollection($column);

        return $this;
    }

    protected function _prepareCollection()
    {

        $collection = $this->_collectionFactory->create();

        $collection->addFieldToFilter('customer_id', ['eq' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Groupon|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'booking_from',
            [
                'header' => __('Desde'),
                'index' => 'booking_from',
                'sortable' => false,
            ]
        );

        $this->addColumn(
            'booking_too',
            [
                'header' => __('Hasta'),
                'index' => 'booking_too',
                'sortable' => false,
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl("*/index/gridreserve", ["_current" => true]);

    }


    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        if ($this->newProductIds)
            return $this->newProductIds;
        return null;
    }




    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField(){
        return $this->_hiddenInputField;
    }

    /**
     * @return array
     */
    public function getEditableFields()
    {
        $fields = [];
        return json_encode($fields);

    }
}

