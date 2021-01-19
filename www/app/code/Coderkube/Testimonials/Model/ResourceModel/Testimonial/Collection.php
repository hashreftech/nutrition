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
namespace Coderkube\Testimonials\Model\ResourceModel\Testimonial;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Coderkube\Testimonials\Model\Testimonial',
            'Coderkube\Testimonials\Model\ResourceModel\Testimonial'
        );
    }
}
