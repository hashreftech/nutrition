<?php

namespace MGS\Brand\Block\Product\ProductList;

use Magento\Customer\Model\Context as CustomerContext;

class Related extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\Framework\DataObject\IdentityInterface
{
    const DEFAULT_PRODUCTS_COUNT = 10;
    protected $_product = null;
    protected $_productsCount;
    protected $httpContext;
    protected $_catalogProductVisibility;
    protected $_productCollectionFactory;
    protected $_coreRegistry = null;
    protected $_brandHelper;
    protected $_brand;
    protected $_storeManager;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        \MGS\Brand\Helper\Data $brandHelper,
        \MGS\Brand\Model\Brand $brand,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    )
    {
        $this->_brand = $brand;
        $this->_storeManager = $storeManager;
        $this->_brandHelper = $brandHelper;
        $this->_coreRegistry = $registry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addData(
            ['cache_lifetime' => 86400, 'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]]
        );
    }

    public function getCacheKeyInfo()
    {
        return [
            'BRAND_RELATED_PRODUCTS',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getProductsCount()
        ];
    }

    protected function _getProductCollection()
    {
        $productIds = array();
        $brand = $this->getBrand();
        if ($brand) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $pCollection = $objectManager->create('MGS\Brand\Model\Product')->getCollection();
            $pCollection->addFieldToFilter('brand_id', ['eq' => $brand->getId()]);
            foreach ($pCollection as $p) {
                if ($p->getProductId() != $this->getProduct()->getId()) {
                    $productIds[] = $p->getProductId();
                }
            }
        }
        $this->setProductsCount($this->getConfig('product_page_settings/limit_related_products'));
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()->order('rand()');
        $collection->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addStoreFilter()
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);
        return $collection;
    }

    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }

    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getBrand()
    {
        $optionId = $this->getProduct()->getMgsBrand();
        if ($optionId) {
            $collection = $this->_brand->getCollection()->addFieldToFilter('option_id', ['eq' => $optionId]);
            if (count($collection)) {
                return $collection->getFirstItem();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if (!$result) {
            return $default;
        }
        return $result;
    }
}
