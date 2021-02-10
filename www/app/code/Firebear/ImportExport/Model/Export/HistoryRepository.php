<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio GmbH. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export;

use Firebear\ImportExport\Api\Data\ExportHistoryInterface;
use Firebear\ImportExport\Api\ExHistoryRepositoryInterface;
use Firebear\ImportExport\Logger\Logger;
use Firebear\ImportExport\Model\ResourceModel\Export\History as ExportHistoryResource;
use Firebear\ImportExport\Model\ResourceModel\Export\History\CollectionFactory as ExportCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class HistoryRepository
 *
 * @package Firebear\ImportExport\Model\Export
 */
class HistoryRepository implements ExHistoryRepositoryInterface
{

    /**
     * @var ExportHistoryResource
     */
    protected $resource;

    /**
     * @var HistoryFactory
     */
    protected $exportFactory;

    /**
     * @var ExportCollectionFactory
     */
    protected $exportCollectionFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * JobRepository constructor.
     *
     * @param ExportHistoryResource $resource
     * @param HistoryFactory $exportFactory
     * @param ExportCollectionFactory $exportCollectionFactory
     * @param Logger $logger
     */
    public function __construct(
        ExportHistoryResource $resource,
        HistoryFactory $exportFactory,
        ExportCollectionFactory $exportCollectionFactory,
        Logger $logger
    ) {
        $this->resource = $resource;
        $this->exportFactory = $exportFactory;
        $this->exportCollectionFactory = $exportCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ExportHistoryInterface $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the job: %1',
                $exception->getMessage()
            ));
        }

        return $history;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($historyId)
    {
        $history = $this->exportFactory->create();
        $this->resource->load($history, $historyId);

        if (!$history->getId()) {
            throw new NoSuchEntityException(__('Export History with id "%1" does not exist.', $historyId));
        }

        return $history;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ExportHistoryInterface $history)
    {
        try {
            $this->resource->delete($history);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the job: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($historyId)
    {
        return $this->delete($this->getById($historyId));
    }
}
