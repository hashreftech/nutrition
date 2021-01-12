<?php
namespace Sparsh\ProductLabel\Block\Adminhtml;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Import
 * @package Knawat\Dropshipping\Block\Adminhtml
 */
class ProductUrls extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $storeManager;
    /**
     * Settings constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getFileUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $fileUrl = $mediaUrl."sparsh/product_label_image/";
    }
}
