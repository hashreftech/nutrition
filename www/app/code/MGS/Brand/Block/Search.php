<?php

namespace MGS\Brand\Block;

class Search extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;
    protected $_brandHelper;
    protected $_brand;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MGS\Brand\Helper\Data $brandHelper,
        \MGS\Brand\Model\Brand $brand,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_brand = $brand;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if (!$this->getConfig('general_settings/enabled')) return;
        parent::_construct();
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if (!$result) {
            return $default;
        }
        return $result;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}