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
namespace Webkul\BookingSystem\Plugin\Block\Order\Item\Renderer;

use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer as ItemRenderer;

class DefaultRenderer
{

    /**
     * Update translation of the customoption title
     *
     * @param ItemRenderer $subject,
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetItemOptions(
        ItemRenderer $subject,
        \Closure $proceed
    ) {
        $result = $proceed();
        if (isset($result)) {
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
