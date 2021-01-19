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

class Edit extends \Coderkube\Testimonials\Controller\Adminhtml\Testimonial
{
    public function execute()
    {
        $model =$this->testimonialTemplate;
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }
        $this->_coreRegistry->register('testimonial_item', $model);
        $this->_view->loadLayout();
        $this->_setActiveMenu('Coderkube_Testimonials::coderkube_testimonial_first');
        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Testimonial');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Testimonial');
            $breadcrumbLabel = __('Create  Testimonial');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Testimonial'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getTestimonialId() ? $model->getTitle() : __('New Testimonial')
        );
        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);
        
        $values = $this->_getSession()->getFormData(true);
        if ($values) {
            $model->addData($values);
        }
        $this->_view->renderLayout();
    }
}
