<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyDefinition;
use BlueMedia\ShopwarePayment\Entity\GatewaySalesChannel\GatewaySalesChannelDefinition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class DatabaseUninstall
{
    private const TABLE_NAMES = [
        GatewayCurrencyDefinition::ENTITY_NAME,
        GatewaySalesChannelDefinition::ENTITY_NAME,
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
