<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 21/12/20
 * Time: 07:20 PM
 */
namespace OY\Catalog\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    private $eavSetupFactory;


    private $categorySetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0){

            $eavSetup = $this->eavSetupFactory->create( [ 'setup' => $setup ] );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'planning_type',
                [
                    'group' => 'Product Details',
                    'sort_order' => 99,
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Tipo de Plan (Semanal, Mensual o Anual)',
                    'input' => 'select',
                    'source' => \OY\Catalog\Model\Entity\Attribute\Source\PlanningType::class,
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_on_front' => true,
                    'visible_in_advanced_search' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                    'apply_to' => 'virtual'
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'number_access',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Número de accesos (0 es accesos ilimitados)',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'virtual',
                    'sort_order' => 100,
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.0.2') < 0){

            $eavSetup = $this->eavSetupFactory->create( [ 'setup' => $setup ] );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'planning_type',
                [
                    'group' => 'Product Details',
                    'sort_order' => 99,
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Tipo de Plan (Semanal, Mensual o Anual)',
                    'input' => 'select',
                    'source' => \OY\Catalog\Model\Entity\Attribute\Source\PlanningType::class,
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_on_front' => true,
                    'visible_in_advanced_search' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                    'apply_to' => 'virtual'
                ]
            );

        }
    }


}
