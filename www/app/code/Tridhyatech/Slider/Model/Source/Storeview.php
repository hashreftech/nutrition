<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\Source;
use \Magento\Store\Model\StoreRepository;

class Storeview extends \Magento\Framework\DataObject
    implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Rate
     */
    protected $_storeRepository;

    /**
     * @param StoreRepository      $storeRepository
     */
    public function __construct(
        StoreRepository $storeRepository
    ) {
        $this->_storeRepository = $storeRepository;
    }

    public function toOptionArray()
   {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $storeRepository = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

       $storeManagerDataList = $storeRepository->getStores();
       $options = array();
       foreach ($storeManagerDataList as $key => $value)
       {
           $options[] = ['label' => $value['name'],  'value' => $key ];
       }
       return $options;
   }
}
