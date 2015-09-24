<?php

namespace TurgayOzgur\C2C\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();

        $connection->addColumn(
            'catalog_product_entity',
            'customer_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'default' => null,
                'comment' => 'Customer\'s product.',
                'unsigned' => true
            ]
        );

        $connection->addForeignKey(
            $installer->getFkName(
                'catalog_product_entity',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'catalog_product_entity',
            'customer_id',
            'customer_entity',
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->endSetup();
    }
}