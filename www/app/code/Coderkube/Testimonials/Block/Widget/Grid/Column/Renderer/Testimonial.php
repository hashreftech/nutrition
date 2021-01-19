<?php
/**
 * Coderkube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Coderkube
 * @package     Coderkube_Testimonials
 * @copyright   Copyright (c) 2018 Coderkube
 */
namespace Coderkube\Testimonials\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Registry;

class Testimonial extends AbstractRenderer
{
    /**
     * @var Registry
     */
    public $registry;
    /**
     * @var AttributeFactory
     */
    public $attributeFactory;
    /**
     * Manufacturer constructor.
     * @param AttributeFactory $attributeFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        AttributeFactory $attributeFactory,
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
    
        $this->attributeFactory = $attributeFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
        $this->_storeManager=$storeManager;
    }
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        // Get default value:
        $value = parent::_getValue($row);
        $baseUrl=$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($value == "") {
            $value="profile.png";
             $html = "No Image uploaded";
        } else {
            $imageSrc=$baseUrl.'ck/testimonial/'.$value;
            $html='<img src='.$imageSrc.' width=150 height=100 >';
        }
       
        return $html;
    }
}
