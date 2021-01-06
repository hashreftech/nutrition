<?php
namespace Gym\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Context;

class AddToCartBeforeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
 
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
	\Magento\Catalog\Model\Product $product,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
	$this->_product = $product;
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
    }
 
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
	$product = $this->_product->load($observer->getRequest()->getParam('product'));
	if ($product && $product->getTypeId() == 'booking') {
	    if ($this->getLoggedInCustomerId()) {
		return $this;
	    } else {
		$this->_messageManager->addError('Debe Iniciar Sesión antes de realizar una Reservación.');
		$observer->getRequest()->setParam('product', false);
	    }
	}
 
        return $this;
    }

    public function getLoggedInCustomerId() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomerId();
        }
        return false;
    }
}
