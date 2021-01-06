<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Ui\Component\Listing\Grid\Column;
class Displayon implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
			$dataResult=array();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cmsPage = $objectManager->get('\Magento\Cms\Model\Page')->getCollection();
                foreach ($cmsPage as $cms)
                {
            $dataResult[0]["value"][] =  array('value'=> $cms->getidentifier(),'label'=>$cms->getTitle());
            }
            $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
            $categories = $categoryCollection->create();
            $categories->addAttributeToSelect('*');
                foreach ($categories as $category) {
                	if($category['entity_id']!=2){
               $dataResult[0]["value"][]=array('value'=>$category->getEntityid(),'label'=>$category->getName());
       			}
                }
			      $productCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $products = $productCollection->create();
            $products->addAttributeToSelect('*')->addAttributeToFilter('visibility', '4');
            foreach ($products as $productsdata) {
            $dataResult[0]["value"][]=array('value'=>$productsdata->getSku(),'label'=>$productsdata->getName());
                }
            foreach ($dataResult as  $array1) {
                 foreach ($array1 as  $value) {
                      return $value;
                  }
             }
      }
  }

