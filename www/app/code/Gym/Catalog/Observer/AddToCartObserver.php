<?php
namespace Gym\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * @property \Magento\Store\Model\StoreManagerInterface _storeManager
 */
class AddToCartObserver implements ObserverInterface
{
    /**
     * AddToCartObserver constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\Event\Manager $eventManager
    )
    {
        $this->_storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->order = $order;
        $this->_messageManager = $messageManager;
        $this->_checkoutSession = $_checkoutSession;
        $this->quoteManagement = $quoteManagement;
        $this->redirect = $redirect;
        $this->quote = $quote;
        $this->_customerSession = $customerSession;
        $this->cart = $cart;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
        $this->_eventManager = $eventManager;
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
        try {
            $items = $observer->getItems();
            foreach ($items as $item) {
                if ($item->getProductType() != 'booking') {
                    return $this;
                }
            }

            $store = $this->_storeManager->getStore();
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();

            $customerRecord = $this->customerRepository->getById($this->getLoggedInCustomerId());

            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($customerRecord->getEmail());

            $quote = $this->quote->create();
            $quote->setStore($store);

            $customer = $this->customerRepository->getById($customer->getEntityId());
            $quote->setCurrency();
            $quote->assignCustomer($customer);

            $allItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
            foreach ($allItems as $item) {
                if ($item->getProductType() == 'booking') {
                    $quote->addItem($item);
                }
            }

            if (!$customer->getDefaultShipping()) {
                $defaultAdress = [
                    'shipping_address' => [
                        'firstname' => 'Gym',
                        'lastname' => 'Pro',
                        'street' => 'Gym Address',
                        'city' => 'Montevideo',
                        'country_id' => 'UY',
                        'region' => 'Montevideo',
                        'postcode' => '10400',
                        'telephone' => '0123456789'
                    ]
                ];
                $quote->getBillingAddress()->addData($defaultAdress['shipping_address']);
                $quote->getShippingAddress()->addData($defaultAdress['shipping_address']);
            }

            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(false)
                ->collectShippingRates()
                ->setShippingMethod('pickup_pickup');
            $quote->setPaymentMethod('free');
            $quote->setInventoryProcessed(false);
            $quote->save();

            $quote->getPayment()->importData(['method' => 'free']);
            $quote->collectTotals()->save();

            $order = $this->quoteManagement->submit($quote);
            $order->setEmailSent(0);

            $allItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
            foreach ($allItems as $item) {
                $itemId = $item->getItemId();
                $this->cart->removeItem($itemId)->save();
            }

            $this->_eventManager->dispatch(
                'checkout_onepage_controller_success_action',
                ['order_ids' => [$order->getId()]]
            );

            $this->_messageManager->addSuccess(__('La Reservación se realizó exitosamente.'));
        } catch (Exception $e) {
            $this->_messageManager->addError(__('Ocurrió un error realizando la Reservación.'));
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
