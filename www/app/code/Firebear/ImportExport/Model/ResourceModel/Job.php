<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\ResourceModel;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\MetadataPool;
use Firebear\ImportExport\Api\Data\ImportInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

/**
 * Class Job
 *
 * @package Firebear\ImportExport\Model\ResourceModel
 */
class Job extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * @param Context       $context
     * @param MetadataPool  $metadataPool
     * @param EntityManager $entityManager
     * @param string        $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        $connectionName = null
    ) {
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('firebear_import_jobs', 'entity_id');
    }

    /**
     * @param AbstractModel $object
     * @param mixed         $value
     *
     * @return mixed
     * @throws NoSuchEntityException
     */

    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ImportInterface::class)->getEntityConnection();
    }
}
