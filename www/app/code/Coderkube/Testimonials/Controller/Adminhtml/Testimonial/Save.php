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

class Save extends \Coderkube\Testimonials\Controller\Adminhtml\Testimonial
{
    public $uploaderFactory;

    public $allowedExtensions = ['csv'];

    public $fileId = 'image';

    public function execute()
    {    	

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        $data1 = $this->getRequest()->getPostValue();
        $files = $this->getRequest()->getFiles('image');
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/testimonial'));
        }
        $template = $this->testimonialTemplate;
        $id = (int)$request->getParam('id');
        if ($id) {
            $template->load($id);
        }
        try {
            $data = $request->getParams();
             //start block upload image
            if (isset($files) && isset($files['name']) && $files['name']!= '') {
                /*
                * Save image upload
                */
                try {
                    $base_media_path = 'ck/testimonial/';
                    $uploader = $this->uploader->create(
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $imageAdapter = $this->adapterFactory->create();
                    $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $mediaDirectory = $this->filesystem
                                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath($base_media_path)
                    );
                    $data['image'] = str_replace(" ", "_", $result['name']);
                    $template->setData('image', $data['image']);
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                $template->setData('image', '');
            }
            
            $template->setData('name', $request->getParam('name'));
            $template->setData('email', $request->getParam('email'));
            $template->setData('content', $request->getParam('content'));
            $template->setData('status', $request->getParam('status'));
            $template->setData('rate', $request->getParam('rate'));
            $template->setData('company_name', $request->getParam('company_name'));
            $template->setData('website', $request->getParam('website'));
            $template->save();

            $this->messageManager->addSuccess(__('Testimonial is saved.'));
            $this->_getSession()->setFormData(false);
        } catch (LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $template->getTestimonialId(), '_current' => true]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $template->getTestimonialId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
