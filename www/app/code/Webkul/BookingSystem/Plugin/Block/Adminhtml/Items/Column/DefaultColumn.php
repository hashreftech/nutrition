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
namespace Webkul\BookingSystem\Plugin\Block\Adminhtml\Items\Column;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn as ItemDefaultColumn;

class DefaultColumn
{

    /**
     * Update translation of the customoption title
     *
     * @param ItemDefaultColumn $subject,
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetOrderOptions(
        ItemDefaultColumn $subject,
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
