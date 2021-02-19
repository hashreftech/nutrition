<?php

namespace MGS\Brand\Helper;

class Data extends \MGS\Core\Helper\Data
{
    protected $_storeManager;
    protected $_config = [];
    protected $_filterProvider;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    )
    {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
    }

    public function getConfig($key, $store = null)
    {
        if ($store == null || $store == '') {
            $store = $this->_storeManager->getStore()->getId();
        }
        $store = $this->_storeManager->getStore($store);
        $config = $this->scopeConfig->getValue(
            'brand/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $config;
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBrandUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $this->getConfig('general_settings/route');
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

}