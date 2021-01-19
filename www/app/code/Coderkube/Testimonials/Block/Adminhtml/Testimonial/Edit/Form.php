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

namespace Coderkube\Testimonials\Block\Adminhtml\Testimonial\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    
    public function getModel()
    {
        return $this->_coreRegistry->registry('testimonial_item');
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->getModel();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Testimonial Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getTestimonialId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getTestimonialId()]);
        }

        if ($model->getImage() == "") {
            $img="";
        } else {
            $img="ck/testimonial/".$model->getImage();
        }
        $fieldset->addField(
            'Name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'value' => $model->getName()
            ]
        );
        $fieldset->addField(
            'Email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'value' => $model->getEmail(),
                'class' => 'validate-email'
            ]
        );

        $fieldset->addField(
            'Company Name',
            'text',
            [
                'name' => 'company_name',
                'label' => __('Company Name'),
                'title' => __('Company Name'),
                'value' => $model->getCompanyName()
            ]
        );
        $fieldset->addField(
            'Website',
            'text',
            [
                'name' => 'website',
                'label' => __('Website'),
                'title' => __('Website'),
                'value' => $model->getWebsite()
            ]
        );
        $fieldset->addField(
            'Content',
            'textarea',
            [
                'name' => 'content',
                'label' => __('Message'),
                'title' => __('Message'),
                'required' => true,
                'value' => $model->getContent()
            ]
        );
        $fieldset->addField(
            'Testimonial Image',
            'image',
            [
              'title' => __('Profile image'),
              'label' => __('Profile image'),
              'name' => 'image',
              'note' => 'Allow image type: jpg, jpeg, gif, png',
              'required' => false,
            'value' => $img
            
            ]
        );
  
        $fieldset->addField(
            'rate',
            'select',
            [
            'label' => __('Rating Star'),
            'title' => __('Rating Star'),
            'name' => 'rate',
            'required' => true,
            'options' => [
                            '1' => __('1 Star'),
                            '2' => __('2 Star'),
                            '3'=> __('3 Star'),
                            '4'=> __('4 Star'),
                            '5'=> __('5 Star')
                        ],
            'value' => $model->getRate()
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'status',
            'required' => true,
            'options' => ['1' => __('Enable'), '0' => __('Disable'),'2'=> __('Pending')],
            'value' => $model->getStatus()
            ]
        );

          $form->setAction($this->getUrl('*/*/delete'));
          $form->setAction($this->getUrl('*/*/save'));
          $form->setUseContainer(true);
          $this->setForm($form);

          return parent::_prepareForm();
    }
}
