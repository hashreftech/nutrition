<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace  Magebees\Newproduct\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\Newproduct\Block\Adminhtml\Product\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Newproduct::newproduct');
    }
}
