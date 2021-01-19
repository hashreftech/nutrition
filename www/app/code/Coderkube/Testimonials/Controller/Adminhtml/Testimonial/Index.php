<?php

/**
 * Coderkube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Coderkube
 * @package     Coderkube_Testimonial
 * @copyright   Copyright (c) 2017 Coderkube
 */

namespace Coderkube\Testimonials\Controller\Adminhtml\Testimonial;

use Coderkube\Testimonials\Controller\Adminhtml\Testimonial;

class Index extends Testimonial
{
    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Coderkube_Testimonials::coderkube_testimonial_first');
        $resultPage->getConfig()->getTitle()->prepend(__('Testimonial'));

        return $resultPage;
    }
}
