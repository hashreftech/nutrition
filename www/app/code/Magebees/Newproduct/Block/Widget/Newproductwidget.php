<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Block\Widget;

class Newproductwidget extends \Magebees\Newproduct\Block\Newproduct implements \Magento\Widget\Block\BlockInterface
{
    
    
    public function addData(array $arr)
    {
        
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
 
    public function _toHtml()
    {
        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }
}
