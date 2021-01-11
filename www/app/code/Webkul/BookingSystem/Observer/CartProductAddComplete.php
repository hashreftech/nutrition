<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_BookingSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\BookingSystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Webkul\BookingSystem\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;

class CartProductAddComplete implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @var \Webkul\BookingSystem\Model\QuoteFactory
     */
    protected $_quote;

    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @param RequestInterface                            $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\CartFactory         $cart
     * @param \Webkul\BookingSystem\Helper\Data           $bookingHelper
     * @param \Webkul\BookingSystem\Model\QuoteFactory    $quote
     * @param QuoteCollection                             $quoteCollection
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\CartFactory $cart,
        \Webkul\BookingSystem\Helper\Data $bookingHelper,
        \Webkul\BookingSystem\Model\QuoteFactory $quote,
        QuoteCollection $quoteCollection
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_cart = $cart;
        $this->_bookingHelper = $bookingHelper;
        $this->_quote = $quote;
        $this->_quoteCollection = $quoteCollection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->_request->getParams();
        $helper = $this->_bookingHelper;
        $cartModel = $this->_cart->create();
        $itemId = 0;
        $productId = 0;
        $cart = $cartModel->getQuote();
        foreach ($cart->getAllItems() as $item) {
            $itemId = $item->getId();
            $productId = $item->getProductId();
        }

        if ($itemId > 0 && $productId > 0) {
            if ($helper->isBookingProduct($productId)) {
                $parentId = $helper->getParentSlotId($productId);
                $result = $this->processSlotData($data, $productId);
                if ($result['error']) {
                    $this->_messageManager->addNotice(__($result['msg']));
                    throw new \Magento\Framework\Exception\LocalizedException($result['msg']);
                }
                try {
                    $collection = $this->_quoteCollection->create();
                    $item = $helper->getDataByField($itemId, 'item_id', $collection);
                    if (!$item) {
                        $slotId = (int) $data['slot_id'];
                        $data =  [
                            'item_id' => $itemId,
                            'slot_id' => $slotId,
                            'parent_slot_id' => $parentId,
                            'quote_id' => $cart->getId(),
                        ];
                        $this->_quote->create()->setData($data)->save();
                    }
                } catch (\Exception $e) {
                    $this->_bookingHelper
                        ->logDataInLogger("Observer_CartProductAddComplete execute : ".$e->getMessage());
                }
            }
        }
    }

    /**
     * Process Slot Data
     *
     * @param array $data
     * @param int   $productId
     *
     * @return array
     */
    private function processSlotData($data, $productId)
    {
        $result = ['error' => false];
        try {
            $helper = $this->_bookingHelper;
            $parentId = $helper->getParentSlotId($productId);
            if ($parentId != $data['parent_id']) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }

            $slotId = (int) $data['slot_id'];
            if ($slotId == 0) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Observer_CartProductAddComplete processSlotData : ".$e->getMessage());
        }
        return $result;
    }
}
