<?php

namespace MGS\Brand\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '=')) {
            $setup->getConnection()->dropTable($setup->getTable('mgs_brand_product'));
            $setup->getConnection()->dropTable($setup->getTable('mgs_brand_store'));
            $setup->getConnection()->dropTable($setup->getTable('mgs_brand'));
            $table = $setup->getConnection()
                ->newTable($setup->getTable('mgs_brand'))
                ->addColumn(
                    'brand_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Brand Id'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    250,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'url_key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    250,
                    ['nullable' => false],
                    'Url Key'
                )
                ->addColumn(
                    'small_image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['default' => null],
                    'Small Image'
                )
                ->addColumn(
                    'image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['default' => null],
                    'Image'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['default' => null],
                    'Description'
                )
                ->addColumn(
                    'meta_keywords',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['default' => null],
                    'Meta Keywords'
                )
                ->addColumn(
                    'meta_description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['default' => null],
                    'Meta Description'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Status'
                )
                ->addColumn(
                    'is_featured',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Featured Brand'
                )
                ->addColumn(
                    'sort_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Sort Order'
                )
                ->addColumn(
                    'option_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Option Id'
                )
                ->setComment('Brands');
            $setup->getConnection()->createTable($table);
            $table = $setup->getConnection()
                ->newTable($setup->getTable('mgs_brand_product'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'brand_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Brand Id'
                )
                ->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Id'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Position'
                )
                ->setComment('Products of Brand');
            $setup->getConnection()->createTable($table);
            $table = $setup->getConnection()
                ->newTable($setup->getTable('mgs_brand_store'))
                ->addColumn(
                    'brand_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Brand Id'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Store Id'
                )
                ->addIndex(
                    $setup->getIdxName('mgs_brand_store', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $setup->getFkName('mgs_brand_store', 'brand_id', 'mgs_brand', 'brand_id'),
                    'brand_id',
                    $setup->getTable('mgs_brand'),
                    'brand_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('mgs_brand_store', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Brands of Store');
            $setup->getConnection()->createTable($table);
            $setup->endSetup();
        }

        $setup->endSetup();
    }
}
