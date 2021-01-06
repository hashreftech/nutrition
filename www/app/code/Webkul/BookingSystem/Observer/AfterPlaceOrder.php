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

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @var \Webkul\BookingSystem\Model\BookedFactory
     */
    protected $_booked;

    /**
     * @param \Webkul\BookingSystem\Helper\Data         $bookingHelper
     * @param \Webkul\BookingSystem\Model\BookedFactory $booked
     */
    public function __construct(
        \Webkul\BookingSystem\Helper\Data $bookingHelper,
        \Webkul\BookingSystem\Model\BookedFactory $booked
    ) {
        $this->_bookingHelper = $bookingHelper;
        $this->_booked = $booked;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $orderIds = $observer->getEvent()->getData('order_ids');
            $orderId = $orderIds[0];
            $order = $this->_bookingHelper->getOrder($orderId);
            $orderedItems = $order->getAllItems();
            foreach ($orderedItems as $item) {
                $this->setBookedSlotsInfo($item, $order);
            }
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterPlaceOrder execute : ".$e->getMessage());
        }
    }

    /**
     * Set Booking Slots Info
     *
     * @param object $item
     * @param object $order
     */
    private function setBookedSlotsInfo($item, $order)
    {
        try {
            $time = time();
            $helper = $this->_bookingHelper;
            $orderId = $order->getId();
            $customerId = (int) $order->getCustomerId();
            $customerEmail = $order->getCustomerEmail();
            $quoteItemId = $item->getQuoteItemId();
            $bookingData = $helper->getDetailsByQuoteItemId($quoteItemId);
            $itemId = $item->getId();
            $qty = $item->getQtyOrdered();
            $productId = $item->getProductId();
            if (!$bookingData['error']) {
                $slotId = $bookingData['slot_id'];
                $parentId = $bookingData['parent_slot_id'];
                $slotData = $helper->getSlotData($slotId, $parentId, $productId);
                $info = [
                    'order_id'          =>      $orderId,
                    'order_item_id'     =>      $itemId,
                    'item_id'           =>      $bookingData['item_id'],
                    'product_id'        =>      $productId,
                    'slot_id'           =>      $slotId,
                    'parent_slot_id'    =>      $parentId,
                    'customer_id'       =>      $customerId,
                    'customer_email'    =>      $customerEmail,
                    'qty'               =>      $qty,
                    'booking_from'      =>      $slotData['booking_from'],
                    'booking_to'        =>      $slotData['booking_to'],
                    'booking_too'        =>      $slotData['booking_to'],
                    'time'              =>      $time,
                ];
                $this->_booked->create()->setData($info)->save();
                $helper->checkBookingProduct($productId);
            }
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Observer_AfterPlaceOrder setBookedSlotsInfo : ".$e->getMessage());
        }
    }
}
