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

namespace Coderkube\Testimonials\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

abstract class Testimonial extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
     const ADMIN_RESOURCE = 'Coderkube_Testimonials::coderkube_testimonial';

     /**
      * Core registry
      *
      * @var \Magento\Framework\Registry
      */
    public $coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;
     /**
      * @var \Magento\Framework\Image\AdapterFactory
      */
    public $adapterFactory;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    public $uploader;
    public $filesystem;
    public $testimonialTemplate;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        UploaderFactory $uploader,
        \Coderkube\Testimonials\Model\Testimonial $testimonialTemplate
    ) {
       
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->uploader = $uploader;
        $this->testimonialTemplate = $testimonialTemplate;
         parent::__construct($context);
    }
}
