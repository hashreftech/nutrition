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

namespace Coderkube\Testimonials\Controller\Adminhtml\Testimonial;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

use Magento\Framework\App\Filesystem\DirectoryList;

class Delete extends \Coderkube\Testimonials\Controller\Adminhtml\Testimonial
{
    
    /**
     * Delete Testimonial Template
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();

        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/testimonial'));
        }
        $template = $this->testimonialTemplate;
        $id = (int)$request->getParam('id');

        if ($id) {
            $template->load($id);
        }
        try {
             $template->load($id)->delete();
             $this->messageManager->addSuccess(__('Testimonial is delete.'));
            $this->_getSession()->setFormData(false);
        } catch (\Exception $e) {
             $this->messageManager->addError(nl2br($e->getMessage()));
        }
         return $resultRedirect->setPath('*/*/');
    }
}
