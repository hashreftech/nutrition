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

namespace Coderkube\Testimonials\Block\Adminhtml\Testimonial;

class Edit extends \Magento\Backend\Block\Template
{
    public function getModel()
    {
        return $this->_coreRegistry->registry('testimonial_item');
    }
    public function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'action-back'
            ]
        );
        $this->getToolbar()->addChild(
            'reset_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Reset'),
                'onclick' => 'window.location.href = window.location.href',
                'class' => 'reset'
            ]
        );
        if ($this->getRequest()->getParam('id', false)) {
            $this->getToolbar()->addChild(
                'remove_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Delete Testimonial'),
                    'onclick' => "window.location.href = '" . $this->getDeleteUrl() . "'",
                    'class' => 'remove'
                ]
            );
        }
            $this->getToolbar()->addChild(
                'save_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Save Testimonial'),
                    'data_attribute' => [
                        'role' => 'testimonial-save',
                    ],
                    'class' => 'save primary',
                ]
            );
        return parent::_prepareLayout();
    }
    /**
     * Return edit flag for block
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEditMode()
    {
        if ($this->getModel()->getTestimonialId()) {
            return true;
        }
        return false;
    }
    /**
     * Return header text for form
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->getRequest()->getParam('id', false)) {
            return __('Edit Template');
        }
        return __('New Testimonial');
    }
    /**
     * Return form block HTML
     * @return string
     */
    public function getForm()
    {
        $getLayout = $this->getLayout()->createBlock('Coderkube\Testimonials\Block\Adminhtml\Testimonial\Edit\Form');
        return  $getLayout->toHtml();
    }
    /**
     * Return action url for form
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
    /**
     * Return delete url for customer group
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getRequest()->getParam('id')]);
    }
    /**
     * Retrieve Save As Flag
     * @return int
     */
    public function getSaveAsFlag()
    {
        return $this->getRequest()->getParam('_save_as_flag') ? '1' : '';
    }
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit']);
    }
    /**
     * Getter for single store mode check
     * @return boolean
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore(true)->getId();
    }
}
