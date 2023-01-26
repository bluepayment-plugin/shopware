<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1671709416CreateGatewaySalesChannelTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1671461405;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `blue_media_gateway_sales_channel` (
                `id` BINARY(16) NOT NULL,
                `gateway_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                `active` TINYINT(1) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`gateway_id`, `sales_channel_id`),
                CONSTRAINT `fk.blue_media_gateway_sales_channel.gateway_id` 
                    FOREIGN KEY (`gateway_id`) 
                       REFERENCES `blue_media_gateway` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.blue_media_gateway_sales_channel.sales_channel_id` 
                    FOREIGN KEY (`sales_channel_id`) 
                        REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
