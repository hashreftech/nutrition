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

class AfterRefundOrder implements ObserverInterface
{
    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @var \Webkul\BookingSystem\Model\Booked\CollectionFactory
     */
    protected $bookedCollection;

    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @param \Webkul\BookingSystem\Helper\Data       $bookingHelper
     */
    public function __construct(
        \Webkul\BookingSystem\Helper\Data $bookingHelper,
        \Webkul\BookingSystem\Model\ResourceModel\Booked\CollectionFactory $bookedCollection,
        \Magento\Sales\Api\OrderItemRepositoryInterface $itemRepository
    ) {
        $this->_bookingHelper = $bookingHelper;
        $this->bookedCollection = $bookedCollection;
        $this->itemRepository = $itemRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $creditmemo = $observer->getEvent()->getCreditmemo();
            foreach ($creditmemo->getItems() as $item) {
                if ($item->getBackToStock()) {
                    $this->updateBookedSlotsInfo($item, $creditmemo);
                }
            }
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_AfterRefundOrder execute : ".$e->getMessage());
        }
    }

    /**
     * Set Booking Slots Info
     *
     * @param object $item
     * @param object $order
     */
    private function updateBookedSlotsInfo($item, $creditmemo)
    {
        try {
            $helper = $this->_bookingHelper;
            $itemId = $item->getOrderItemId();
            $orderItem = $this->itemRepository->get($itemId);
            $orderId = $creditmemo->getOrderId();
            $customerId = (int) $creditmemo->getCustomerId();
            $quoteItemId = $orderItem->getQuoteItemId();
            $bookingData = $helper->getDetailsByQuoteItemId($quoteItemId);

            $productId = $item->getProductId();
            $returnQty = $item->getQty();

            if (!$bookingData['error']) {
                $slotId = $bookingData['slot_id'];
                $parentId = $bookingData['parent_slot_id'];
                $collection = $this->bookedCollection->create()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('order_item_id', $itemId)
                    ->addFieldToFilter('item_id', $bookingData['item_id'])
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('slot_id', $slotId)
                    ->addFieldToFilter('parent_slot_id', $parentId)
                    ->addFieldToFilter('customer_id', $customerId);
                if ($collection->getSize()) {
                    foreach ($collection as $data) {
                        if ($returnQty==$data->getQty()) {
                            $data->delete();
                        } else {
                            $data->setQty($data->getQty()-$returnQty)->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Observer_AfterRefundOrder updateBookedSlotsInfo : ".$e->getMessage());
        }
    }
}
