<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\ResourceModel;

class Grid extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('slider','slider_id');
    }
}
