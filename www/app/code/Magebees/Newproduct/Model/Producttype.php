<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Model;

class Producttype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 2, 'label' => __('Auto')],['value' => 1, 'label' => __('Manually')], ['value' => 0, 'label' => __('Both')]];
    }

    public function toArray()
    {
        return [1 => __('Manually'),2=>__('Auto'),0 => __('Both')];
    }
}
