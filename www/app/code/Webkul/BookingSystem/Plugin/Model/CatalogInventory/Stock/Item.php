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
namespace Webkul\BookingSystem\Plugin\Model\CatalogInventory\Stock;

class Item
{
    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @param \Webkul\BookingSystem\Helper\Data $bookingHelper
     */
    public function __construct(\Webkul\BookingSystem\Helper\Data $bookingHelper)
    {
        $this->_bookingHelper = $bookingHelper;
    }

    public function afterGetQty(\Magento\CatalogInventory\Model\Stock\Item $subject, $result)
    {
        try {
            $productId = $subject->getProductId();
            $helper = $this->_bookingHelper;
            if ($helper->isBookingProduct($productId, true)) {
                return $helper->getTotalBookingQty($productId);
            }
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Plugin_Model_CatalogInventory_Stock_Item afterGetQty : ".$e->getMessage());
        }
        return $result;
    }
}
