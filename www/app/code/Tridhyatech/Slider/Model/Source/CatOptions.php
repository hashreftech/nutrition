<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\Source;

class CatOptions  implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $dataResult=array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $collection->create();
        $categories->addAttributeToSelect('*');
        foreach ($categories as $category) {
             if($category['entity_id']!=2){
                $dataResult[]=array('value'=>$category->getEntityid(),'label'=>$category->getName());
             }
        }
        return $dataResult;
  }
}
