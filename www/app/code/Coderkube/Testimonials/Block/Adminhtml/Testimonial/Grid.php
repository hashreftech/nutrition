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

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_blockGroup = 'Coderkube_Testimonials';
        $this->_controller = 'adminhtml_testimonial';
        $this->_addButtonLabel = __('Add New Testimonial');
        $this->buttonList->add(
            'add',
            [
                'label' => __('Add New Testimonial'),
                'onclick' => 'setLocation(\'' . $this->getUrl('testimonial/testimonial/edit') . '\')',
                'class' => 'action-default scalable add primary'
            ]
        );
    }
}
