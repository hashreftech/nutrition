<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Migration\PreJob;

use Firebear\ImportExport\Model\Migration\DbConnection;
use Firebear\ImportExport\Model\Migration\PreJobInterface;

/**
 * @inheritdoc
 */
class Truncate implements PreJobInterface
{
    /**
     * @var DbConnection
     */
    protected $dbConnection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param DbConnection $dbConnection
     * @param string $table
     */
    public function __construct(
        DbConnection $dbConnection,
        string $table
    ) {
        $this->dbConnection = $dbConnection;
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function job()
    {
        $this->dbConnection->getDestinationChannel()->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dbConnection->getDestinationChannel()->truncateTable($this->table);
        $this->dbConnection->getDestinationChannel()->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
