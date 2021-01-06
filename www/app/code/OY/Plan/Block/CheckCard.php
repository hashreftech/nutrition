<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09/07/19
 * Time: 09:28 PM
 */

namespace OY\Card\Block;


class CheckCard extends \Magento\Catalog\Block\Product\AbstractProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }
}