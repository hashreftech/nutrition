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

class BeforeUpdateCart implements ObserverInterface
{
    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @param \Webkul\BookingSystem\Helper\Data $helper
     */
    public function __construct(\Webkul\BookingSystem\Helper\Data $helper)
    {
        $this->_bookingHelper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $info = $observer->getEvent()->getInfo()->getData();
        $cart = $observer->getEvent()->getCart();
        $quote = $cart->getQuote();
        if ($info) {
            foreach ($quote->getAllVisibleItems() as $item) {
                if (array_key_exists($item->getId(), $info)) {
                    $itemId = (int) $item->getId();
                    $productId = $item->getProductId();
                    if ($this->_bookingHelper->isBookingProduct($productId)) {
                        try {
                            $requestedQty = (int) $info[$itemId]['qty'];
                            $parentId = $this->_bookingHelper->getParentSlotId($productId);
                            $itemData = $item->getBuyRequest()->getData();
                            $slotId = (int) $itemData['slot_id'];
                        } catch (\Exception $e) {
                            $this->_bookingHelper
                                ->logDataInLogger("Observer_BeforeViewCart execute : ".$e->getMessage());
                        }
                        if (!$this->processSlotData($slotId, $parentId, $productId, $item, $requestedQty)) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('Slot quantity not available')
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Process Slot Data
     *
     * @return bolean
     */
    private function processSlotData($slotId, $parentId, $productId, $item, $requestedQty)
    {
        $slotData = $this->_bookingHelper->getSlotData($slotId, $parentId, $productId);
        $availableQty = $slotData['qty'];
        if ($requestedQty > $availableQty) {
            return false;
        } else {
            return true;
        }
    }
}
