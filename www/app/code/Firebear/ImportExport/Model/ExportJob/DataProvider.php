<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\ExportJob;

use Firebear\ImportExport\Model\ResourceModel\ExportJob\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    protected $pool;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param CollectionFactory $exportCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        CollectionFactory $exportCollectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->jsonDecoder   = $jsonDecoder;
        $this->collection    = $exportCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->addEventToResult()->getItems();
        $jsonFields = [
            \Firebear\ImportExport\Model\ExportJob::BEHAVIOR_DATA,
            \Firebear\ImportExport\Model\ExportJob::EXPORT_SOURCE,
            \Firebear\ImportExport\Model\ExportJob::SOURCE_DATA
        ];
        foreach ($items as $job) {
            $data = $job->getData();
            foreach ($jsonFields as $name) {
                if ($data[$name]) {
                    $tempData = $this->jsonDecoder->decode($data[$name]);
                    unset($data[$name]);
                    $data += $tempData;
                }
            }
            $this->loadedData[$job->getId()] = $data;
        }

        $data = $this->dataPersistor->get('export_job');
        if (!empty($data)) {
            $job = $this->collection->getNewEmptyItem();
            $job->setData($data);
            $this->loadedData[$job->getId()] = $job->getData();
            $this->dataPersistor->clear('export_job');
        }

        return $this->loadedData;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
