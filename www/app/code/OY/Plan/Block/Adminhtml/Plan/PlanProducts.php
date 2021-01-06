<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/06/19
 * Time: 12:13 PM
 */
namespace OY\Plan\Block\Adminhtml\Plan;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;
use Magento\Customer\Controller\RegistryConstants;

class PlanProducts extends \Magento\Backend\Block\Widget\Grid\Extended
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
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
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
        $this->setId('plan_list');
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
                        'catalog/product/new', ['set'=>4,'type' => 'virtual', 'type_virtual'=>2]
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
        $collection->addFieldToFilter('type_id', 'virtual');
        $collection->addAttributeToFilter('type_virtual', 2);
        $collection->addAttributeToSelect('name');

        //$collection->addFieldToFilter('customer_id', ['eq' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)]);
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
        /*$this->addColumn(
            'product_member',
            [
                'header' => __('Product member'),
                'index' => 'product_member',
                'sortable' => false,
                'renderer' => 'DSIELAB\Customer\Block\Adminhtml\Customer\Edit\Fieldset\Item\Product',
            ]
        );*/
        //$this->addColumn('entity_id', ['header' => __('ID'), 'index' => 'entity_id']);
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        /*$this->addColumn('status', ['header' => __('Estado'), 'index' => 'status']);
        $this->addColumn('plan_count', ['header' => __('Cantidad de Plan'), 'index' => 'plan_count']);
        $this->addColumn('plan', ['header' => __('Plan'), 'index' => 'plan']);

        $this->addColumn(
            'from',
            [
                'header' => __('Desde'),
                'type' => 'datetime',
                'index' => 'from',
                'sortable' => false,
            ]
        );

        $this->addColumn(
            'to',
            [
                'header' => __('Hasta'),
                'type' => 'datetime',
                'index' => 'to',
                'sortable' => false,
            ]
        );*/

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
        return $this->getUrl("catalog/product/edit", ["_current" => true, 'id'=>$item->getId(), 'type_virtual'=>2]);
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
