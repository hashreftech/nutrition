<?php
namespace OY\Registry\Model\ResourceModel\Registry;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('OY\Registry\Model\Registry','OY\Registry\Model\ResourceModel\Registry');
    }

}
