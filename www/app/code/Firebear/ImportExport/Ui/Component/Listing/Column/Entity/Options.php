<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Ui\Component\Listing\Column\Entity;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var \Firebear\ImportExport\Model\ExportFactory
     */
    protected $export;

    /**
     * @var \Magento\ImportExport\Model\Source\Export\Entity
     */
    protected $entity;

    public function __construct(
        \Firebear\ImportExport\Model\ExportFactory $export,
        \Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Export\Options $entity
    ) {
        $this->export = $export;
        $this->entity = $entity;
    }

    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [];

        return $this->options;
    }
}
