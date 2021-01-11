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
namespace Webkul\BookingSystem\Plugin\Model\Catalog;

class Product
{
    public function afterGetIsVirtual(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId() == "booking") {
            return true;
        }

        return $result;
    }
}
