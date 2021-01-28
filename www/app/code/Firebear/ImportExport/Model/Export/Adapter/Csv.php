<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\Adapter;

use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\Csv as AbstractAdapter;
use Magento\Framework\App\CacheInterface;

/**
 * Csv Export Adapter
 */
class Csv extends AbstractAdapter
{
    /**
     * Adapter Data
     *
     * @var []
     */
    protected $_data;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Initialize Adapter
     *
     * @param Filesystem $filesystem
     * @param CacheInterface $cache
     * @param null $destination
     * @param [] $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Filesystem $filesystem,
        CacheInterface $cache,
        $destination = null,
        array $data = []
    ) {
        $this->_data = $data;
        $this->_cache = $cache;
        if (isset($data['behavior_data'])) {
            $data = $data['behavior_data'];
            $this->_delimiter = $data['separator'] ?? $this->_delimiter;
            $this->_enclosure = $data['enclosure'] ?? $this->_enclosure;
        }

        parent::__construct(
            $filesystem,
            $destination
        );
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws \Exception
     * @return $this
     */
    public function writeRow(array $rowData)
    {
        $exportByPage = $this->_cache->load('export_by_page');

        if ((null === $this->_headerCols) && ($exportByPage == 0)) {
            $this->setHeaderCols(array_keys($rowData));
        }
        if (null === $this->_headerCols) {
            $headerColumns = array_keys($rowData);
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
        }
        $this->_fileHandler->writeCsv(
            array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols)),
            $this->_delimiter,
            $this->_enclosure
        );
        return $this;
    }
}
