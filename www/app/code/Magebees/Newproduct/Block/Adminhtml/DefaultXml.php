<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Newproduct\Block\Adminhtml;

class DefaultXml extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;">
		<span>&lt;referenceContainer name="content"&gt;</br>&lt;block class="Magebees\Newproduct\Block\Newproduct" name="newproduct" ifconfig="newproduct/setting/enable"/&gt;</br>&lt;/referenceContainer&gt;</span>	
		</div>';
    }
}
