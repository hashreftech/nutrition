<?php

namespace Sparsh\ProductLabel\Model\Config\Source;

/**
 * Class ShapeOptions
 * @package Sparsh\ProductLabel\Model\Config\Source
 */
class ShapeOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Select Shape'), 'value'=>''],
            ['label' => __('Rectangle'), 'value'=>'rectangle'],
            ['label' => __('Oval'), 'value'=>'oval']
        ];
        return $this->_options;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $columns = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();

        $type = \Magento\Framework\DB\Ddl\Table::TYPE_TEXT;
        $columns[$attributeCode] = [
            'type' => $type,
            'length' => 255,
            'unsigned' => false,
            'nullable' => true,
            'default' => null,
            'extra' => null,
            'comment' => $attributeCode . ' column',
        ];

        return $columns;
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];

        $index = sprintf('IDX_%s', strtoupper($this->getAttribute()->getAttributeCode()));
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];

        $sortable = $this->getAttribute()->getUsedForSortBy();
        if ($sortable && $this->getAttribute()->getFrontend()->getInputType() != 'multiselect') {
            $index = sprintf('IDX_%s_VALUE', strtoupper($this->getAttribute()->getAttributeCode()));

            $indexes[$index] = [
                'type' => 'index',
                'fields' => [$this->getAttribute()->getAttributeCode() . '_value'],
            ];
        }

        return $indexes;
    }
}
