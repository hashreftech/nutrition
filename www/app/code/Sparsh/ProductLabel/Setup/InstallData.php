<?php
namespace Sparsh\ProductLabel\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;

/**
 * Install data
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    protected $_attributeSet;
    protected $_resourceProduct;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeSet $attributeSet,
        ResourceProduct $resourceProduct
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_attributeSet    = $attributeSet;
        $this->_resourceProduct = $resourceProduct;
    }

    /**
     * install data method
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label_select',
            [
                'type' => 'varchar',
                'input' => 'select',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Label Type',
                'group' => 'General',
                'source' => \Sparsh\ProductLabel\Model\Config\Source\LabelOptions::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'used_for_promo_rules' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => true,
                'used_in_product_listing' => true
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label',
            [
                'type' => 'varchar',
                'input' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Label',
                'group' => 'General',
                'visible' => true,
                'used_for_promo_rules' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'used_in_product_listing' => true

            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label_background_color',
            [
                'type' => 'varchar',
                'input' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Label Background Color',
                'group' => 'General',
                'visible' => true,
                'used_for_promo_rules' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'default' => '#ff0000',
                'used_in_product_listing' => true

            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label_color',
            [
                'type' => 'varchar',
                'input' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Label Color',
                'group' => 'General',
                'visible' => true,
                'used_for_promo_rules' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'default' => '#ffffff',
                'used_in_product_listing' => true

            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label_shape',
            [
                'type' => 'varchar',
                'input' => 'select',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Label Shape',
                'group' => 'General',
                'source' => \Sparsh\ProductLabel\Model\Config\Source\ShapeOptions::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'used_for_promo_rules' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => true,
                'used_in_product_listing' => true
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'product_label_image',
            [
                'group' => 'Product Details',
                'type' => 'varchar',
                'label' => 'Product Label Image',
                'input' => 'image',
                'backend' => \Sparsh\ProductLabel\Model\Product\Attribute\Backend\File::class,
                'frontend' => '',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'unique' => false,
                'used_in_product_listing' => true
            ]
        );

        $entityType = $this->_resourceProduct->getEntityType();
        $attributeSetCollection = $this->_attributeSet->setEntityTypeFilter($entityType);
        foreach ($attributeSetCollection as $attributeSet) {
            $eavSetup->addAttributeToSet(
                "catalog_product",
                $attributeSet->getAttributeSetName(),
                "General",
                "product_label_image"
            );
        }
    }
}
