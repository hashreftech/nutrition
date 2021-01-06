<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Block;

use Magento\Customer\Model\Session;

class Slider extends \Magento\Framework\View\Element\Template
{

    protected $_page;
    private $sliderGroupCollectionFactory;

    private $storeManager;

    private $customerSession;

    private $templateProcessor;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Tridhyatech\Slider\Model\ResourceModel\Grid\CollectionFactory $sliderGroupCollectionFactory,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\Registry $registry,
        Session $customerSession
    ) {

        $this->sliderGroupCollectionFactory = $sliderGroupCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->customerSession = $customerSession;
        $this->_page = $page;
        $this->registry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
    public function getSliderCollectionCms()
    {
        $storeid = $this->getStoreView()->getId();
        $cms = $this->getCurrentCmsPage();
        $sliderGroupCollection = $this->sliderGroupCollectionFactory->create();
        $sliderGroupCollection->addFieldToFilter(array('icon','video'),array(array('neq' => "NULL"),array('neq' => "NULL"),));
        $sliderGroupCollection->setOrder('sortorder', 'ASC')->addFieldToFilter('status', 1)->addFieldToFilter('store_id',array('finset' => $storeid));
        $sliderGroupCollection->addFieldToFilter('cms',array('finset' => $cms));
        return $sliderGroupCollection;
    }
    public function getSliderCollectionForProducts()
    {
        $storeid = $this->getStoreView()->getId();
        $product = $this->getProduct()->getSku();
        $sliderGroupCollection = $this->sliderGroupCollectionFactory->create();
        $sliderGroupCollection->addFieldToFilter(array('icon','video'),array(array('neq' => "NULL"),array('neq' => "NULL"),));
        $sliderGroupCollection->setOrder('sortorder', 'ASC')->addFieldToFilter('status', 1)->addFieldToFilter('store_id',array('finset' => $storeid));
        $sliderGroupCollection->addFieldToFilter('products',array('finset' => $product));
        return $sliderGroupCollection;
    }
    public function getSliderCollectionForCategories()
    {
        $storeid = $this->getStoreView()->getId();
        $cat = $this->getCategory()->getEntityId();
        $sliderGroupCollection = $this->sliderGroupCollectionFactory->create();
        $sliderGroupCollection->addFieldToFilter(array('icon','video'),array(array('neq' => "NULL"),array('neq' => "NULL"),));
        $sliderGroupCollection->setOrder('sortorder', 'ASC')->addFieldToFilter('status', 1)->addFieldToFilter('store_id',array('finset' => $storeid));
        $sliderGroupCollection->addFieldToFilter('displayon',array('finset' => $cat));
        return $sliderGroupCollection;
    }

    public function getImageUrl($icon)
    {
        $mediaUrl = $this->storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaUrl.'slider/tmp/icon/'.$icon;
       /* if (!empty($icon)) {
          return $imageUrl;
        }
        else{
           $imageNullUrl = '';
           return $imageNullUrl;
        }*/
         return $imageUrl;

    }
     public function getBaseUrl()
    {
        $mediaUrl = $this->storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

         return $mediaUrl;
    }

    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
  // function to get current cms page of magento 2
  public function getCurrentCmsPage()
  {
    $pageIdentifier = $this->_page->getIdentifier();
    return $pageIdentifier;
  }
  // function to get current category page of magento 2
  public function getCategory(){
        try {
            return $this->registry->registry('current_category');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return ['response' => 'Category Not Found'];
        }
    }
 // function to get current product page of magento 2
    public function getProduct(){
        try {
            return $this->registry->registry('current_product');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return ['response' => 'Product Not Found'];
        }
    }
 // function to get current storeview of magento 2
    public function getStoreView(){
        $storeview = $this->storeManager->getStore();
        return $storeview;
    }
}
