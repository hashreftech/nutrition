<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\Source;

class CmsOptions  implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        // for cms page options
            $CmsOptions=array();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cmsPage = $objectManager->get('\Magento\Cms\Model\Page')->getCollection();
            foreach ($cmsPage as $cms)
                {
            $CmsOptions[] =  array('value'=> $cms->getidentifier(),'label'=>$cms->getTitle());
            }
            return $CmsOptions;
  }
}
