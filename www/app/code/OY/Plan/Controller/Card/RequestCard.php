<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01/11/19
 * Time: 09:18 PM
 */

namespace OY\Card\Controller\Card;

use Magento\Framework\App\RequestInterface;

class RequestCard extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Session
     */
    protected $customerSession;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession)
    {
        $this->_pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    /**
     * Allow only customers - redirect to login page
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $this->customerSession->setBeforeAuthUrl($this->_url->getUrl(
                'card/card/requestcard'
            ));
        }
        return parent::dispatch($request);
    }

    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Pedir Tarjeta');

        return $resultPage;
    }
}