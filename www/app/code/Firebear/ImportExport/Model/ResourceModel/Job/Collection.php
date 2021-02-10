<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\ResourceModel\Job;

use Firebear\ImportExport\Model\Job as ModelJob;
use Firebear\ImportExport\Model\ResourceModel\AbstractCollection;
use Firebear\ImportExport\Model\ResourceModel\Job as ResourceModelJob;

/**
 * Class Collection
 *
 * @package Firebear\ImportExport\Model\ResourceModel\Job
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            ModelJob::class,
            ResourceModelJob::class
        );
    }
}
