<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1671461401CreateGatewayCurrencyTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1671461401;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `blue_media_gateway_currency` (
                `id` BINARY(16) NOT NULL,
                `currency_id` BINARY(16) NOT NULL,
                `gateway_id` BINARY(16) NOT NULL,
                `min_cart_amount` DECIMAL(14,2) UNSIGNED NULL,
                `max_cart_amount` DECIMAL(14,2) UNSIGNED NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`currency_id`, `gateway_id`),
                CONSTRAINT `fk.blue_media_gateway_currency.gateway_id`
                    FOREIGN KEY (`gateway_id`) 
                        REFERENCES `blue_media_gateway` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.blue_media_gateway_currency.currency_id`
                    FOREIGN KEY (`currency_id`) 
                        REFERENCES `currency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
