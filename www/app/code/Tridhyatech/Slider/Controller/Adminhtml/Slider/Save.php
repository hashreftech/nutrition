<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
//use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Tridhyatech\Slider\Model\GridFactory;
//use Tridhyatech\Slider\Model;

use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

/**
 * Class Save
 * @package Tridhyatech\Slider\Controller\Adminhtml\Slider
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Date filter
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
   // protected $_dateFilter;

    /**
    * @var CacheTypeListInterface
    */
   protected $cache;
   /**
    * @var CacheManager
    */
   protected $cacheManager;

    /**
     * Save constructor.
     * @param Context $context
     * @param gridFactory $gridFactory
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context,
        GridFactory $gridFactory,
        Registry $coreRegistry,
        CacheTypeListInterface $cache,
        CacheManager $cacheManager
        //Date $dateFilter
    )
    {
      //  $this->_dateFilter = $dateFilter;
        $this->_coreRegistry= $coreRegistry;
        $this->gridFactory = $gridFactory;
        parent::__construct($context);
        $this->cache = $cache;
        $this->cacheManager = $cacheManager;

    }
    public function execute()
    {
       $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('slider_id');
        if ($data) {
            $data = $this->_filterFoodData($data);
            $model = $this->_objectManager->create('Tridhyatech\Slider\Model\Slider')->load($id);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $title = $objectManager->create('Tridhyatech\Slider\Model\ResourceModel\Grid\Collection');
            $title->addFieldToFilter('title',$data['title']);
                if($this->getRequest()->getParam('slider_id')){
                $title->addFieldToFilter('slider_id',array('neq' => $this->getRequest()->getParam('slider_id')));
                }
                if($title->getData()){
                  $this->messageManager->addError(__('Slider with same title  already exist please try a diffrent one.'));
                  $this->_redirect('slider/slider/index');
                }
                elseif(empty($data['video']) && empty($data['icon'])){
                    $this->messageManager->addError(__('Please add a Slider image or Video url.'));
                    $this->_redirect('slider/slider/index');
                }
                else{
                 try {
            $rowData = $this->gridFactory->create();
            $rowData->setData($data);
            $rowData->save();
            $this->cache->invalidate(['layout', 'block_html', 'full_page']);
            $this->messageManager->addSuccess(__('Slider data has been successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('slider/slider/edit', ['slider_id' => $rowData->getSliderId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            }
               catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
                }
                $this->_redirect('slider/slider/index');
            }
        }
    }
     public function _filterFoodData(array $rawData)
    {
        $data = $rawData;

         if (isset($data['icon'][0]['name'])) {
            $data['icon'] = $data['icon'][0]['name'];
         } else {
            $data['icon'] = null;
         }
            $prefix = $new = $cms = $products = $prefixproducts = $prefixcms = '';
         if (!empty($data['displayon'])) {
            foreach ($data['displayon'] as  $value) {
                 $new .= $prefix.''.$value.'' ;
                     $prefix = ',';  }
            if (isset($new)) {
                 $data['displayon'] = $new;
            }
            }
         else {
            $data['displayon'] = null;
         }
            $pre = $store = '';
         if (!empty($data['store_id'])) {
            foreach ($data['store_id'] as  $value) {
                $store .= $pre.''.$value.'' ;
                     $pre = ',';
             }
             if (isset($new)) {
                 $data['store_id'] = $store;
             }
             }
         else {
            $data['store_id'] = null;
         }
         if (!empty($data['products'])) {
            foreach ($data['products'] as  $value) {
                 $products .= $prefixproducts.''.$value.'' ;
                     $prefixproducts = ',';  }
            if (isset($products)) {
                 $data['products'] = $products;
            }
            }
         else {
            $data['products'] = null;
         }
         if (!empty($data['cms'])) {
            foreach ($data['cms'] as  $value) {
                 $cms .= $prefixcms.''.$value.'' ;
                     $prefixcms = ',';  }
            if (isset($cms)) {
                 $data['cms'] = $cms;
            }
            }
         else {
            $data['cms'] = null;
         }
        return $data;
    }
    }



