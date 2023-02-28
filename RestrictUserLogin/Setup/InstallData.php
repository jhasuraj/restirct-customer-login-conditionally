<?php
namespace Suraj\RestrictUserLogin\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_activated', [
            'label' => 'Is Activated',
            'system' => 0,
            'position' => 50,
            'sort_order' => 50,
            'visible' => true,
            'note' => '',
            'type' => 'int',
            'input' => 'select',
            'source' => Boolean::class,
            ]
        );

        $this->getEavConfig()->getAttribute('customer', 'customer_activated')->setData('is_user_defined', 1)->setData('is_required', 0)->setData('default_value', 0)->setData('used_in_forms', ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'])->save();

    }

    public function getEavConfig() {
        return $this->eavConfig;
    }
}


