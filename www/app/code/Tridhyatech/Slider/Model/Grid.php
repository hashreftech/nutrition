<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model;
class Grid extends \Magento\Framework\Model\AbstractModel implements \Tridhyatech\Slider\Api\Data\ContactInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'slider';

    protected function _construct()
    {
        $this->_init('Tridhyatech\Slider\Model\ResourceModel\Grid');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
