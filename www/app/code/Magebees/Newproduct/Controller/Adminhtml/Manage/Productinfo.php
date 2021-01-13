<?php
/***************************************************************************
 Extension Name	: New Products 
 Extension URL	: http://www.magebees.com/new-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace  Magebees\Newproduct\Controller\Adminhtml\Manage;

class Productinfo extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
        getBlock('newproduct.product.edit.tab.productinfo')
            ->setProductsNewproduct($this->getRequest()->getPost('products_newproduct', null));
        return $resultLayout;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Newproduct::newproduct');
    }
}
