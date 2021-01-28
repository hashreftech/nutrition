<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Types;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $exportConfig;

    /**
     * @var \Firebear\ImportExport\Model\Export\Dependencies\Config
     */
    protected $diExport;

    /**
     * Options constructor.
     * @param \Firebear\ImportExport\Model\ExportFactory $export
     * @param \Magento\ImportExport\Model\Source\Export\Entity $entity
     */
    public function __construct(
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        \Firebear\ImportExport\Model\Export\Dependencies\Config $configExDi
    ) {
        $this->exportConfig = $exportConfig;
        $this->diExport = $configExDi;
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
        $options = [];
        foreach ($this->exportConfig->getEntities() as $entityName => $entityConfig) {
            $options[$entityName] = [['value' => $entityName, 'label' => __($entityConfig['label'])]];
        }
        $data = $this->diExport->get();
        foreach ($data as $typeName => $type) {
            $childs = [];
            if (isset($type['fields'])) {
                foreach ($type['fields'] as $name => $field) {
                    $childs[] = ['label' => $field['label'], 'value' => $name, 'dep' => $typeName];
                }
            } else {
                $childs[] = ['label' => $type['label'], 'value' => $typeName];
            }
            $options[$typeName] = $childs;
        }
        $this->options = $options;

        return $this->options;
    }
}
