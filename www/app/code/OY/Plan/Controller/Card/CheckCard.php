<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09/07/19
 * Time: 07:57 PM
 */

namespace OY\Card\Controller\Card;

use Magento\Customer\Model\Context;


class CheckCard extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $_customerFactory;

    protected $_customerRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerFactory = $customerFactory;
        $this->httpContext = $httpContext;
        $this->_customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($this->isLoggedIn() && !$this->getLoggedInCustomerId()) {
            $this->_customerSession->start();
        }
        if (!$this->isLoggedIn() || !$this->getLoggedInCustomerId()) {
            $this->_redirect('');
        }

        $customer = $this->_customerRepository->getById($this->getLoggedInCustomerId());
        if ($customer->getCustomAttribute('associate_check_permit') == null ||
            $customer->getCustomAttribute('associate_check_permit')->getValue() == false) {
            $this->_redirect('');
        }

        $searchEmail = $this->getRequest()->getParam('search_email');
        $searchCardCode = $this->getRequest()->getParam('search_card_code');
        $searchDocument = $this->getRequest()->getParam('search_document');

        $collection = $this->_customerFactory->create();
        if ($searchEmail != '') {
            $collection->addFieldToFilter('email', array("like" => '%'.$searchEmail.'%'));
        }

        if ($searchDocument != '') {
            $collection->addAttributeToFilter('customer_card_document', array("like" => '%'.$searchDocument.'%'));
        }

        if ($searchCardCode != '') {
            $collection->joinTable('card_entity', 'customer_id = entity_id',['customer_id','code', 'from'],
                ['code' => array("like" => $searchCardCode.'%')]);
        }


        $resultPage->getLayout()->initMessages();
        $resultPage->getLayout()->getBlock('card_card_checkcard')
            ->setCustomers($searchEmail == '' && $searchCardCode == '' &&  $searchDocument == '' ? [] : $collection->getItems())
            ->setSearchEmail($searchEmail)
            ->setSearchDocument($searchDocument)
            ->setSearchCode($searchCardCode);
        ;
        $resultPage->getConfig()->getTitle()->set('Verificar Tarjeta');

        return $resultPage;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getLoggedInCustomerId() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomerId();
        }
        return false;
    }
}