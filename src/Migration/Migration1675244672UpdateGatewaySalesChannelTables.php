<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1675244672UpdateGatewaySalesChannelTables extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1675244672;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function update(Connection $connection): void
    {
        $this->createAggregationTables($connection);

        if ($this->deprecatedTableExists($connection)) {
            $this->copyActiveGatewaysSalesChannel($connection);
            $this->dropDeprecatedTable($connection);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function deprecatedTableExists(Connection $connection): bool
    {
        $sql = <<<SQL
            SELECT EXISTS (SELECT `TABLE_NAME` FROM `information_schema`.`TABLES`
               WHERE `TABLE_SCHEMA` LIKE :database 
                 AND `TABLE_TYPE` LIKE 'BASE TABLE' 
                 AND `TABLE_NAME` = :table
           );
SQL;
        return (bool)$connection->executeQuery(
            $sql,
            [
                'database' => $connection->getDatabase(),
                'table' => 'blue_media_gateway_sales_channel',
            ]
        )->fetchOne();
    }

    /**
     * @throws Exception
     */
    private function dropDeprecatedTable(Connection $connection): void
    {
        $connection->executeStatement('DROP TABLE IF EXISTS `blue_media_gateway_sales_channel`');
    }

    /**
     * @throws Exception
     */
    private function copyActiveGatewaysSalesChannel(Connection $connection): void
    {
        $sql = <<<'SQL'
        INSERT INTO `blue_media_gateway_sales_channel_active`
            (`gateway_id`, `sales_channel_id`)
            SELECT `gateway_id`, `sales_channel_id`
                FROM `blue_media_gateway_sales_channel`
                WHERE `blue_media_gateway_sales_channel`.`active` = :active
SQL;
        try {
            $connection->executeStatement($sql, ['active' => 1]);
        } catch (UniqueConstraintViolationException $e) {
            // already executed
            return;
        }
    }

    /**
     * @throws Exception
     */
    private function createAggregationTables(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `blue_media_gateway_sales_channel_active` (
                `gateway_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`gateway_id`, `sales_channel_id`),
                CONSTRAINT `fk.blue_media_gateway_sales_channel_active.gateway_id` 
                    FOREIGN KEY (`gateway_id`) 
                       REFERENCES `blue_media_gateway` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.blue_media_gateway_sales_channel_active.sales_channel_id` 
                    FOREIGN KEY (`sales_channel_id`) 
                        REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `blue_media_gateway_sales_channel_enabled` (
                `gateway_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`gateway_id`, `sales_channel_id`),
                CONSTRAINT `fk.blue_media_gateway_sales_channel_enabled.gateway_id` 
                    FOREIGN KEY (`gateway_id`) 
                       REFERENCES `blue_media_gateway` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.blue_media_gateway_sales_channel_enabled.sales_channel_id` 
                    FOREIGN KEY (`sales_channel_id`) 
                        REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }
}
