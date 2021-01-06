<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\Source;

class Display  implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $dataResult=array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          // for product page options
            $productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $products = $productCollectionFactory->create();
            $products->addAttributeToSelect('*')->addAttributeToFilter('visibility', '4');
            foreach ($products as $productsdata) {
            $dataResult[]=array('value'=>$productsdata->getSku(),'label'=>$productsdata->getName());
                }
            return $dataResult;
  }
}
