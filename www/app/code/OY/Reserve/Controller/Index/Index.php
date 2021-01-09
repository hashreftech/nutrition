<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/12/20
 * Time: 08:55 AM
 */
namespace OY\Reserve\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($block = $this->_view->getLayout()->getBlock('manage-reserve')) {

            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $resultPage->getConfig()->getTitle()->set('Mis Reservas');

        return $resultPage;
    }

}
