<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Block;

class Newproduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_orderstatus;
    protected $_npmanualCollection;
    protected $_datetime;
    protected $_collection;
    protected $_stock;
    protected $_config;
    protected $_product_visibility;
    protected $_sliderconfig;
    protected $_moduleManager;
    protected $urlHelper;
    protected $_imageHelper;
    protected $_storeManager;
    protected $_newproducts;
    protected $_productCollectionFactory;
    protected $pager;
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';
    const PAGE_VAR_NAME = 'np';
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magebees\Newproduct\Model\ResourceModel\Customcollection\Collection $npmanualCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Catalog\Model\Product\Visibility $visibilityModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Sales\Model\Order\Status\History $orderstatus,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
    
        $this->_coreResource = $resource;
        $this->urlHelper = $urlHelper;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->visibilityModel = $visibilityModel;
        $this->stockHelper = $stockHelper;
        $this->_npmanualCollection = $npmanualCollection;
        $this->_datetime=$datetime;
        $this->_orderstatus=$orderstatus;
        $this->_objectManager = $objectManager;

        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return $this->_scopeConfig->getValue('newproduct/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getSliderconfig()
    {
        return $this->_scopeConfig->getValue('newproduct/slidersetting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function _toHtml()
    {
    
        $this->_config=$this->getConfig();
        if ($this->_config['enable']=="0") {
            return '';
        }
        
        if (!$this->getTemplate()) {
            $this->setTemplate('new_grid.phtml');
        }
        return parent::_toHtml();
    }
    
    
    public function setWidgetOptions()
    {
        
        $this->setShowHeading((bool)$this->getWdShowHeading());
        $this->setHeading($this->getWdHeading());
        $this->setProductType($this->getWdProductType());
        $this->setDefaultNew($this->getWdDefaultNew());
        $this->setAddDays($this->getWdAddDays());
        $this->setNewproduct($this->getWdNewproduct());
        $this->setCategories($this->getWdCategories());
        $this->setSortBy($this->getWdSortBy());
        $this->setSortOrder($this->getWdSortOrder());
        $this->setProductsPrice((bool)$this->getWdPrice());
        $this->setDescription((bool)$this->getWdDescription());
        $this->setAddToCart((bool)$this->getWdCart());
        $this->setAddToWishlist((bool)$this->getWdWishlist());
        $this->setAddToCompare((bool)$this->getWdCompare());
        $this->setOutOfStock((bool)$this->getWdOutStock());
        $this->setAjaxscrollPage((bool)$this->getWdAjaxscrollPage());
        //Template Settings
        $this->setNoOfProduct((int)$this->getWdNoOfProduct());
        $this->setProductsPerRow((int)$this->getWdProductsPerRow());
        $this->setProductsPerPage($this->getWdProductsPerPage());
        $this->setShowSlider((bool)$this->getWdSlider());
        
        //slider Settings
        $this->setAutoscroll((bool)$this->getWdAutoscroll());
        //$this->setPagination((bool)$this->getWdPagination());
        $this->setNavarrow((bool)$this->getWdNavarrow());
    }
    
    
    public function setConfigValues()
    {
        $this->_config=$this->getConfig();
        $this->_sliderConfig=$this->getSliderconfig();
        
        $this->setEnabled((bool)$this->_config['enable']);
        $this->setShowHeading((bool)$this->_config['show_heading']);
        $this->setProductType($this->_config['product_type']);
        $this->setDefaultNew($this->_config['default_new']);
        $this->setAddDays($this->_config['add_days']);
        $this->setHeading($this->_config['heading']);
        $this->setNewproduct($this->_config['newproduct']);
        $this->setCategories($this->_config['categories']);
        $this->setSortBy($this->_config['sort_by']);
        $this->setSortOrder($this->_config['sort_order']);
        $this->setProductsPrice((bool)$this->_config['price']);
        $this->setDescription((bool)$this->_config['description']);
        $this->setAddToCart((bool)$this->_config['cart']);
        $this->setAddToWishlist((bool)$this->_config['wishlist']);
        $this->setAddToCompare((bool)$this->_config['compare']);
        $this->setOutOfStock((bool)$this->_config['out_stock']);
        $this->setAjaxscrollPage((bool)$this->_config['enable_ajaxscroll_page']);
        //Template Settings
        $this->setNoOfProduct((int)$this->_config['no_of_product']);
        $this->setProductsPerRow((int)$this->_config['products_per_row']);
        $this->setProductsPerPage($this->_config['per_page_value']);
        $this->setShowSlider((bool)$this->_config['slider']);

        //slider Settings
        $this->setAutoscroll((bool)$this->_sliderConfig['autoscroll']);
        //$this->setPagination((bool)$this->_sliderConfig['pagination']);
        $this->setNavarrow((bool)$this->_sliderConfig['navarrow']);
    }
    
    protected function _beforeToHtml()
    {
        
        if ($this->getType()=="Magebees\Newproduct\Block\Widget\Newproductwidget\Interceptor") {
            $this->setWidgetOptions();
        } elseif ($this->getType()=="Magebees\Newproduct\Block\Widget\Newproductwidget") {
            $this->setWidgetOptions();
        } else {
            $this->setConfigValues();
        }
        $this->setProductCollection($this->getNewproductsCollection());
        return parent::_beforeToHtml();
    }
    public function getPagerHtml()
    {
            $total_limit=$this->getNoOfProduct();
            $pagination=$this->getProductsPerPage();
            $page_arr=explode(",", $pagination);
            $limit=[];
        foreach ($page_arr as $page) {
            $limit[$page]=$page;
        }
        if ($this->getProductCollection()->getSize()) {
            if (!$this->pager) {
                 $this->pager = $this->getLayout()->createBlock(
                     'Magento\Catalog\Block\Product\Widget\Html\Pager',
                     'magebees_new.pager'
                 );

                $this->pager->setAvailableLimit($limit)
                ->setLimitVarName('np_limit')
                ->setPageVarName('np')
                ->setShowPerPage(true)
                ->setTotalLimit($total_limit)
                ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
    

    
    /****Get Product collection for Auto Newproduct*****/
    public function getNewAutoCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->visibilityModel->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        )->setPageSize(
            $this->getProductsCount()
        )->setCurPage(
            1
        );
        return $collection;
    }
    /****Get Product collection for manually Newproduct*****/

    public function getNewManualCollection()
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $product_ids=$this->getProductsIds();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('name')
           ->addFinalPrice()
           ->addAttributeToSelect('*')
           ->setStore($storeId)
           ->addStoreFilter($storeId)
           ->addFieldToFilter('entity_id', ['in' =>$product_ids])
           ->addAttributeToFilter('visibility', 4);
        return $collection;
    }
    
    protected function getNewProductsByCreatedDate()
    {
        $days = $this->getAddDays();
        $today = strtotime($this->_localeDate->date()->format('Y-m-d H:i:s'));
        $last = $today - (60*60*24*$days);
        $from = date("Y-m-d H:i:s", $last);
        $to = date("Y-m-d H:i:s", $today);
         $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->visibilityModel->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->addAttributeToSelect('*')
                ->addAttributeToSort('created_at', 'desc')
                ->addAttributeToFilter('created_at', ['from' => $from, 'to' => $to]);
                
        return $collection;
    }
    
    public function getNewproductsCollection()
    {
        switch ($this->getProductType()) {
            case 2:
                if ($this->getDefaultNew()) {
                    $collection = $this->getNewAutoCollection();
                } else {
                    $collection = $this->getNewProductsByCreatedDate();
                }
                break;
            case 1: //Manually
                $collection = $this->getNewManualCollection();
                break;
            case 0: //Both
                if ($this->getDefaultNew()) {
                    $collection1 = $this->getNewAutoCollection();
                } else {
                    $collection1 = $this->getNewProductsByCreatedDate();
                }
                $collection2 = $this->getNewManualCollection();
                        
                $merged_ids = array_unique(array_merge($collection1->getAllIds(), $collection2->getAllIds()));
                 $collection = $this->_productCollectionFactory->create();
                $collection->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' =>$merged_ids]);
                break;
            default:
                $collection = $this->getNewAutoCollection();
                break;
        }
        
        $storeId=$this->_storeManager->getStore()->getId();
        
        $collection ->addMinimalPrice()
                    ->addFinalPrice()
                    ->setStore($storeId)
                    ->addAttributeToFilter('visibility', 4)
                    ->addStoreFilter($storeId);
                
                
        //Display out of stock products
        if (!$this->getOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }
    
        
        //Display By Category
        
        if ($this->getNewproduct()==2) {
            if ($this->getCategories()) {
                 $categories=ltrim($this->getCategories(),",");
                $categorytable = $this->_coreResource->getTableName('catalog_category_product');
                $collection->getSelect()
                        ->joinLeft(['ccp' => $categorytable], 'e.entity_id = ccp.product_id', 'ccp.category_id')
                        ->group('e.entity_id')
                        ->where("ccp.category_id IN (".$categories.")");
            }
        }
        
        //Set Sort Order
        if ($this->getSortOrder()=='rand') {
            $collection->getSelect()->order('rand()');
        } else {
            if($this->getSortBy()=='position')
            {

                $collection->getSelect()->order('e.entity_id ' . $this->getSortOrder());
            }
            else
            {
                 $collection->addAttributeToSort($this->getSortBy(), $this->getSortOrder()); 
            }
        }
        
        $total_limit=$this->getNoOfProduct();
        $collection->getSelect()->limit($total_limit);
        //print_r($collection->getData());
        if (!$this->getShowSlider()) {
            $pagination=$this->getProductsPerPage();
            $page_arr=explode(",", $pagination);
            $limit=[];
            foreach ($page_arr as $page) {
                $limit[$page]=$page;
            }
            $default_limit=current($limit);
         //get values of current page. if not the param value then it will set to 1
            $page=($this->getRequest()->getParam('np'))? $this->getRequest()->getParam('np') : 1;
        //get values of current limit. if not the param value then it will set to 1
            $pageSize=($this->getRequest()->getParam('np_limit'))? $this->getRequest()->getParam('np_limit') :$default_limit;
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);
        }
        
        return $collection;
    }
    
    public function getProductsIds()
    {
        $storeId=$this->_storeManager->getStore()->getId();
        $customcollection=$this->_npmanualCollection->getData();
    
        foreach ($customcollection as $custom) {
		if($custom['entity_id']!='')
		{
            if ($custom['store_id']==$storeId) {
                $product_ids=$custom['entity_id'];
            }
		}
        }
        
        if (empty($product_ids)) {
            foreach ($customcollection as $custom) {
                $store_arr=[0,$storeId];
                foreach ($store_arr as $store) {
                    if ($custom['store_id']==$store) {
                        $product_ids[]=$custom['entity_id'];
                    }
                }
            }
            if (!empty($product_ids)) {
                $new_entityId= implode(",", $product_ids);
                $new= explode(",", $new_entityId);
                $entity=array_unique($new);
            } else {
                return $product_ids=[0];
            }
            return $entity;
        } else {
            $entity= explode(",", $product_ids);
            return $entity;
        }
    }
        
    public function getImageHelper()
    {
        return  $this->imageHelper;
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
            'product' => $product->getEntityId(),
            \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    public function getUniqueSliderKey()
    {
        $key = uniqid();
        return $key;
    }
}
