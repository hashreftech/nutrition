<?php
/**
 * IntegrationInterface
 *
 * @copyright Copyright © 2019 Firebear Studio. All rights reserved.
 * @author    fbeardev@gmail.com
 */

namespace Firebear\ImportExport\Model\Import\Product\Integration;

use Firebear\ImportExport\Model\ResourceModel\Import\Data as ResourceModelData;

/**
 * Interface IntegrationInterface
 * @package Firebear\ImportExport\Model\Import\Product\Integration
 */
interface IntegrationInterface
{
    /**
     * @param bool $verbosity
     * @return mixed
     */
    public function importData($verbosity = false);

    /**
     * @param ResourceModelData $dataSourceModel
     * @return ResourceModelData
     */
    public function setDataSourceModel(ResourceModelData $dataSourceModel);
}
