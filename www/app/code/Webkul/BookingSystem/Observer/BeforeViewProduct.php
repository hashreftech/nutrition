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

class BeforeViewProduct implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @param RequestInterface                  $request
     * @param \Webkul\BookingSystem\Helper\Data $bookingHelper
     */
    public function __construct(
        RequestInterface $request,
        \Webkul\BookingSystem\Helper\Data $bookingHelper
    ) {
        $this->_request = $request;
        $this->_bookingHelper = $bookingHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $data = $this->_request->getParams();
            $productId = $data['id'];
            $this->_bookingHelper->enableOptions($productId);
            $this->_bookingHelper->checkBookingProduct($productId);
        } catch (\Exception $e) {
            $this->_bookingHelper->logDataInLogger("Observer_BeforeViewProduct execute : ".$e->getMessage());
        }
    }
}
