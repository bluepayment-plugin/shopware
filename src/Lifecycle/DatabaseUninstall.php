<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Entity\Gateway\Aggregate\GatewaySalesChannelsActiveDefinition;
use BlueMedia\ShopwarePayment\Entity\Gateway\Aggregate\GatewaySalesChannelsEnabledDefinition;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyDefinition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class DatabaseUninstall
{
    private const TABLE_NAMES = [
        GatewaySalesChannelsActiveDefinition::ENTITY_NAME,
        GatewaySalesChannelsEnabledDefinition::ENTITY_NAME,
        GatewayCurrencyDefinition::ENTITY_NAME,
        'blue_media_gateway_sales_channel', //removed in 1.1.0
        GatewayDefinition::ENTITY_NAME,
    ];

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->dropTables();
    }

    /**
     * @throws Exception
     */
    private function dropTables(): void
    {
        foreach (self::TABLE_NAMES as $tableName) {
            $this->connection->executeStatement(
                sprintf('DROP TABLE IF EXISTS `%s`', $tableName) // can't use parameters for table names in PDO
            );
        }
    }
}
