<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/06/19
 * Time: 04:30 PM
 */
namespace OY\Plan\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface{

    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ){

        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0){

            try {
                /**
                 * Create table 'card_entity'
                 */
                $table = $installer->getConnection()
                    ->newTable(
                        $installer->getTable('plan_entity')
                    )
                    ->addColumn(
                        'value_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'order_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['default' => '0'],
                        'Order ID'
                    )
                    ->addColumn(
                        'plan',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        50,
                        ['nullable' => false],
                        'Plan'
                    )->addColumn(
                        'from',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        null,
                        ['nullable' => false],
                        'From'
                    )->addColumn(
                        'to',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        null,
                        ['nullable' => false],
                        'To'
                    )
                    ->addColumn(
                        'access_number',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Acces Number'
                    )
                    ->addColumn(
                        'access_enabled',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Acces Enabled'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'card_entity',
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->setComment(
                        'Plan'
                    );
                $installer->getConnection()
                    ->createTable($table);

            } catch (\Exception $e) {
                // Do nothing
            }

        }

    }
}
