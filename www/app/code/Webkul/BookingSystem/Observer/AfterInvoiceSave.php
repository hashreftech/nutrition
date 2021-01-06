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

class AfterInvoiceSave implements ObserverInterface
{
    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    private $helper;

    /**
     * @param \Webkul\BookingSystem\Helper\Data $helper
     */
    public function __construct(
        \Webkul\BookingSystem\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getInvoice()->getOrder();
            foreach ($order->getItems() as $item) {
                $this->helper->checkBookingProduct($item->getProductId());
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Observer_AfterInvoiceSave execute : ".$e->getMessage()
            );
        }
    }
}
