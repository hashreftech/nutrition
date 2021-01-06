<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Model\Slider;

use Tridhyatech\Slider\Model\ResourceModel\Grid\CollectionFactory;
use Tridhyatech\Slider\Model\Grid;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    protected $_loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sliderdataCollectionFactory,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ){
        $this->collection = $sliderdataCollectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if(isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();

        foreach($items as $sliderdata)
        {
            $this->_loadedData[$sliderdata->getId()] = $sliderdata->getData();
        }
        foreach ($items as $model) {
            if ($model->getDisplayon()) {
                $d['displayon'] = $model->getDisplayon();
                $d['displayon'] = explode(',', $d['displayon']);
               $fullData = $this->_loadedData;
                $this->_loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $d);

            }
             if ($model->getCms()) {
                $cms['cms'] = $model->getCms();
                $cms['cms'] = explode(',', $cms['cms']);
               $fullData = $this->_loadedData;
                $this->_loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $cms);

            }
             if ($model->getProducts()) {
                $p['products'] = $model->getProducts();
                $p['products'] = explode(',', $p['products']);
               $fullData = $this->_loadedData;
                $this->_loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $p);

            }

            if ($model->getIcon()) {
                $m['icon'][0]['name'] = $model->getIcon();
                $m['icon'][0]['url'] = $this->getMediaUrl().$model->getIcon();
                $fullData = $this->_loadedData;
                $this->_loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $m);
            }
        }
        return $this->_loadedData;
    }
        public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'slider/tmp/icon/';
        return $mediaUrl;
    }
}
