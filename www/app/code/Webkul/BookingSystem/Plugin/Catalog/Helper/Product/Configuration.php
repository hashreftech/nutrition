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
namespace Webkul\BookingSystem\Plugin\Catalog\Helper\Product;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Catalog\Helper\Product\Configuration as ProductConfiguration;

class Configuration
{

    /**
     * Update translation of the customoption title
     *
     * @param ProductConfiguration $subject,
     * @param \Closure $proceed,
     * @param ItemInterface $item
     * @return array
     */
    public function aroundGetCustomOptions(
        ProductConfiguration $subject,
        \Closure $proceed,
        ItemInterface $item
    ) {
        $result = $proceed($item);
        if (is_array($result)) {
            $bookingLables = [
                "Booking From",
                "Booking Till"
            ];
            foreach ($result as $key => $item) {
                if (isset($item['label'])) {
                    if (in_array($item['label'], $bookingLables)) {
                        $result[$key]['label'] = __($item['label']);
                    }
                }
            }
        }
        return $result;
    }
}
