<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\Adapter;

use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\AbstractAdapter;
use Firebear\ImportExport\Model\Output\Xslt;

/**
 * Xml Export Adapter
 */
class Xml extends AbstractAdapter
{
    /**
     * XML Writer
     *
     * @var \XMLWriter
     */
    protected $writer;

    /**
     * Xslt Converter
     *
     * @var \Firebear\ImportExport\Model\Output\Xslt
     */
    protected $xslt;

    /**
     * Xsl Document
     *
     * @var string
     */
    protected $xsl;

    /**
     * Adapter Data
     *
     * @var []
     */
    protected $_data;

    /**
     * Initialize Adapter
     *
     * @param Filesystem $filesystem
     * @param \XMLWriter $writer
     * @param Xslt $xslt
     * @param null $destination
     * @param [] $data
     */
    public function __construct(
        Filesystem $filesystem,
        \XMLWriter $writer,
        Xslt $xslt,
        $destination = null,
        array $data = []
    ) {
        $this->writer = $writer;
        $this->xslt = $xslt;
        $this->_data = $data;

        if (!empty($data['xml_switch']) && isset($data['xslt'])) {
            $this->xsl = $data['xslt'];
        }

        parent::__construct(
            $filesystem,
            $destination
        );
    }

    /**
     * @return $this
     */
    protected function _init()
    {
        $this->writer->openURI('php://output');
        $this->writer->openMemory();
        $this->writer->startDocument("1.0", "UTF-8");
        $this->writer->setIndent(1);
        $this->writer->startElement("Items");

        return $this;
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/xml';
    }

    /**
     * Get contents of export file
     *
     * @return string
     */
    public function getContents()
    {
        $this->writer->endDocument();
        $result = $this->writer->outputMemory();
        return $this->xsl
            ? $this->xslt->convert($result, $this->xsl)
            : $result;
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
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
        if (!empty($rowData)) {
            $this->writer->startElement('item');
            foreach ($rowData as $key => $value) {
                if (is_array($value)) {
                    $this->recursiveAdd($key, $value);
                } elseif (is_string($key)) {
                    $this->writer->writeElement($key, $value);
                }
            }
            $this->writer->endElement();
        }
        return $this;
    }

    /**
     * @param $key
     * @param array $data
     */
    protected function recursiveAdd($key, array $data)
    {
        if (!empty($data)) {
            if (!is_numeric($key)) {
                $this->writer->startElement($key);
            }
            foreach ($data as $ki => $values) {
                if (is_array($values)) {
                    $this->recursiveAdd($ki, $values);
                } else {
                    $this->writer->writeElement($ki, $values);
                }
            }
            if (!is_numeric($key)) {
                $this->writer->endElement();
            }
        }
    }
}
