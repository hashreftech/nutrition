<?php
/**
 * Sparsh ProductLabel Module
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */

namespace Sparsh\ProductLabel\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;


/**
 * Class InstallSchema
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context )
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('sparsh_product_label_rules')
                )
                ->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Rule Id'
                )->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => false],
                    'Rule Name'
                )->addColumn(
                    'is_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Is  Active'
                )->addColumn(
                    'product_label_select',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => false],
                    'Product Label Select'
                )->addColumn(
                    'product_label',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => true],
                    'Product Label'
                )->addColumn(
                    'product_label_background_color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => true],
                    'Product Label Background Color'
                )->addColumn(
                    'product_label_color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => true],
                    'Product Label Color'
                )->addColumn(
                    'product_label_shape',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => true],
                    'Product Label Shape'
                )->addColumn(
                    'product_label_image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    55,
                    ['nullable' => true],
                    'Product Label Image'
                )->addColumn(
                    'from_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'From Date'
                )->addColumn(
                    'to_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true,'default' => null],
                    'To Date'
                )->addColumn(
                    'priority',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => true,'default' => 1],
                    'Priority'
                )->addColumn(
                    'conditions_serialized',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Catalog Conditions'
                )->addColumn(
                    'creation_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created DateTime'
                )->addColumn(
                    'updation_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Modified DateTime'
                )->addIndex(
                    $installer->getIdxName(
                        'sparsh_product_label_rules',
                        ['name'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['name'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
                )->setComment('Sparsh Product Labels Table');

            $installer->getConnection()->createTable($table);

            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('sparsh_product_label_rules_store')
                )
                ->addColumn(
                    'product_label_rules_store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Primary Key'
                )->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Rule Id'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Store Id'
                )->addForeignKey(
                    $installer->getFkName(
                        'sparsh_product_label_rules_store',
                        'rule_id',
                        'sparsh_product_label_rules',
                        'rule_id'
                    ),
                    'rule_id',
                    $installer->getTable('sparsh_product_label_rules'),
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        'sparsh_product_label_rules_store',
                        'store_id',
                        'store',
                        'store_id'
                    ),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Sparsh Product Label Rules Store Table');
            $installer->getConnection()->createTable($table);

            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('sparsh_product_label_rules_customer_group')
                )
                ->addColumn(
                    'product_label_rules_customer_group_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Primary Key'
                )->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Rule Id'
                )->addColumn(
                    'customer_group_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer Group Id'
                )->addForeignKey(
                    $installer->getFkName(
                        'sparsh_product_label_rules_customer_group',
                        'rule_id',
                        'sparsh_product_label_rules',
                        'rule_id'
                    ),
                    'rule_id',
                    $installer->getTable('sparsh_product_label_rules'),
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        'sparsh_product_label_rules_customer_group',
                        'customer_group_id',
                        'customer_group',
                        'customer_group_id'
                    ),
                    'customer_group_id',
                    $installer->getTable('customer_group'),
                    'customer_group_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment(
                    'Sparsh Product Label Rules Customer Group Table'
                );
            $installer->getConnection()->createTable($table);

        }
        $installer->endSetup();
    }
}
