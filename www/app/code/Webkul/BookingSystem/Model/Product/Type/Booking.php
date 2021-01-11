<?php
namespace Webkul\BookingSystem\Model\Product\Type;

class Booking extends \Magento\Catalog\Model\Product\Type\Virtual
{
    /**
     * Return true if product has options
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function hasOptions($product)
    {
        return true;
    }

    /**
     * Check if product has required options
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function hasRequiredOptions($product)
    {
        return true;
    }
}
