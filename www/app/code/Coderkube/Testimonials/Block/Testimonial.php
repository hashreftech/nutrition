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
namespace Coderkube\Testimonials\Block;

use Coderkube\Testimonials\Model\ResourceModel\Testimonial\Collection as TestimonialCollection;
use Magento\Framework\ObjectManagerInterface;

class Testimonial extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Coderkube\Testimonials\Model\ResourceModel\Testimonial\CollectionFactory $TestimonialCollectionFactory,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonialCollectionFactory = $TestimonialCollectionFactory;
        $this->objectManager = $objectManager;
    }
    public function getTestimonials()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_testimonialCollectionFactory
                ->create()
                ->addFilter('status', 1);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
    public function getMediaUrl()
    {
        $media_dir = $this->_storeManager
                     ->getStore()
                     ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
    public function getBaseUrl()
    {
        $base_dir = $this->_storeManager
                ->getStore()
                ->getBaseUrl();
        return $base_dir;
    }
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTestimonials()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'reward.history.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15,20=>20])
                ->setShowPerPage(false)->setCollection(
                    $this->getTestimonials()
                );
            $this->setChild('pager', $pager);
            $this->getTestimonials()->load();
        }
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * function to get rewards point transaction of customer
     *
     * @return reward transaction collection
     */
    public function getTestimonialHistory()
    {
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_testimonialCollectionFactory
                ->create()
                ->addFilter('status', 1);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $logger->info("Here reward collection: ".$collection->getSelect());
        $logger->info("Here reward collection: Page:".$page." Page size :".$pageSize);
        return $collection;
    }
}
