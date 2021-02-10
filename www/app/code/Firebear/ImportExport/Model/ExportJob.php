<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model;

use Magento\Framework\Model\AbstractModel;
use Firebear\ImportExport\Api\Data\ExportInterface;
use Firebear\ImportExport\Model\ResourceModel\ExportJob as ResourceModelExportJob;

/**
 * Class ExportJob
 *
 * @package Firebear\ImportExport\Model
 */
class ExportJob extends AbstractModel implements ExportInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModelExportJob::class);
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @return string|null
     */
    public function getCron()
    {
        return $this->getData(self::CRON);
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->getData(self::FREQUENCY);
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->getData(self::ENTITY);
    }

    /**
     * @return string
     */
    public function getBehaviorData()
    {
        return  $this->getData(self::BEHAVIOR_DATA);
    }

    /**
     * @return string
     */
    public function getSourceData()
    {
        return $this->getData(self::SOURCE_DATA);
    }

    /**
     * @return string|null
     */
    public function getFileUpdatedAt()
    {
        return $this->getData(self::FILE_UPDATED_AT);
    }

    /**
     * @return string
     */
    public function getExportSource()
    {
        return $this->getData(self::EXPORT_SOURCE);
    }

    /**
     * @return string
     */
    public function getXslt()
    {
        return $this->getData(self::XSLT);
    }

    /**
     * @param $jobId
     *
     * @return ExportJob
     */
    public function setId($jobId)
    {
        $this->setData(self::ENTITY_ID, $jobId);
    }

    /**
     * @param $title
     *
     * @return ExportJob
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);
    }

    /**
     * @param $isActive
     *
     * @return ExportJob
     */
    public function setIsActive($isActive)
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @param $cron
     *
     * @return ExportJob
     */
    public function setCron($cron)
    {
        $this->setData(self::CRON, $cron);
    }

    /**
     * @param $frequency
     *
     * @return ExportJob
     */
    public function setFrequency($frequency)
    {
        $this->setData(self::FREQUENCY, $frequency);
    }

    /**
     * @param $entity
     *
     * @return ExportJob
     */
    public function setEntity($entity)
    {
        $this->setData(self::ENTITY, $entity);
    }

    /**
     * @param $behavior
     *
     * @return ExportJob
     */
    public function setBehaviorData($behavior)
    {
        $this->setData(self::BEHAVIOR_DATA, $behavior);
    }

    /**
     * @param $source
     *
     * @return ExportJob
     */
    public function setSourceData($source)
    {
        $this->setData(self::SOURCE_DATA, $source);
    }

    /**
     * @param $date
     *
     * @return ExportJob
     */
    public function setFileUpdatedAt($date)
    {
        $this->setData(self::FILE_UPDATED_AT, $date);
    }

    /**
     * @param $source
     *
     * @return ExportJob
     */
    public function setExportSource($source)
    {
        $this->setData(self::EXPORT_SOURCE, $source);
    }

    /**
     * @param $xslt
     *
     * @return ExportJob
     */
    public function setXslt($xslt)
    {
        return $this->setData(self::XSLT, $xslt);
    }
}
