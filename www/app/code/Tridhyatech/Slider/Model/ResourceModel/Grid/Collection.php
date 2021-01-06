<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'slider_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {

        $this->_init(
            'Tridhyatech\Slider\Model\Grid',
            'Tridhyatech\Slider\Model\ResourceModel\Grid'
        );
    }
}
