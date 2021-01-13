<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Block\Adminhtml\Product;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Magebees_Newproduct';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Products'));
        $this->buttonList->update('delete', 'label', __('Delete Block'));
    }
}
