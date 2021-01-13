<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Add Newproduct'));
    }
    protected function _prepareLayout()
    {
          $this->addTab(
              'newproduct',
              [
                  'label' => __('Add New Products'),
                  'url' => $this->getUrl('newproduct/manage/productinfo', ['_current' => true]),
                  'class' => 'ajax'
              ]
          );
          return parent::_prepareLayout();
    }
}
