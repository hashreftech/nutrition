<?php
/**
 * Coderkube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Coderkube
 * @package     Coderkube_Testimonial
 * @copyright   Copyright (c) 2017 Coderkube
 */
namespace Coderkube\Testimonials\Model;

use Magento\Framework\Exception\LocalizedException as CoreException;

class Testimonial extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Coderkube\Testimonials\Model\ResourceModel\Testimonial');
    }
}
