<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02/12/20
 * Time: 01:00 AM
 */

namespace OY\Customer\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $customerSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $attributeCode = 'customer_image';

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type' => 'text',
                'label' => 'Customer File/Image',
                'input' => 'file',
                'source' => '',
                'required' => false,
                'visible' => true,
                'position' => 200,
                'system' => false,
                'backend' => ''
            ]
        );

        // used this attribute in the following forms
        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode)
            ->addData(
                ['used_in_forms' => [
                    'adminhtml_customer',
                    'adminhtml_checkout',
                    'customer_account_create',
                    'customer_account_edit'
                ]
                ]);

        $attribute->save();
        $setup->endSetup();
    }
}
