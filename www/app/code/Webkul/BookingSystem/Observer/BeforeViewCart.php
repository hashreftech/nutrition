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

class BeforeViewCart implements ObserverInterface
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
        try {
            $this->_bookingHelper->checkStatus();
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_BeforeViewCart execute : ".$e->getMessage());
        }
    }
}
