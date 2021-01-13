<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/

namespace Magebees\Newproduct\Model;

class Customcollection extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Newproduct\Model\ResourceModel\Customcollection');
    }
}
