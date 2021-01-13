<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('cms_page'),
            'content',
            'content',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => '64M',
                'comment' => 'Content'
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable('cms_block'),
            'content',
            'content',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => '64M',
                'comment' => 'Content'
            ]
        );
    }
}