<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06/06/19
 * Time: 09:49 AM
 */
namespace OY\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;
use Magento\Customer\Controller\RegistryConstants;

class PlanGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_hiddenInputField = 'plan';


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
        \OY\Plan\Model\ResourceModel\Plan\CollectionFactory $collectionFactory,
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
        $this->setId('plan_listing');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);

    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label'     => 'Add',
                'onclick'   => 'setLocation(\'' . $this->getUrl(
                        'customer/plan/new', ['customerid' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)]
                    ) . '\')',
                'class'   => 'task'
            ))->toHtml();
        return $html.$addButton;
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
        //$this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Groupon|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        //$this->addColumn('value_id', ['header' => __('ID'), 'index' => 'value_id']);
        $this->addColumn('plan', ['header' => __('Plan'), 'index' => 'plan']);
        $this->addColumn('access_number', ['header' => __('No. de accesos'), 'index' => 'access_number']);
        $this->addColumn('access_enabled', ['header' => __('No. de accesos disponibles'), 'index' => 'access_enabled']);

        $this->addColumn(
            'from',
            [
                'header' => __('Desde'),
                'type' => 'date',
                'index' => 'from',
                'sortable' => false,
            ]
        );

        $this->addColumn(
            'to',
            [
                'header' => __('Hasta'),
                'type' => 'date',
                'index' => 'to',
                'sortable' => false,
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl("*/index/grid", ["_current" => true]);

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
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl("*/plan/edit", ["_current" => true, 'id'=>$item->getId(), 'customerid'=>$item->getData('customer_id')]);
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
