<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1671461399CreateGatewayTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1671461399;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `blue_media_gateway` (
                `id` BINARY(16) NOT NULL,
                `external_id` MEDIUMINT UNSIGNED NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` LONGTEXT NULL,
                `type` VARCHAR(255) NOT NULL,
                `bank_name` VARCHAR(255) NULL,
                `logo_media_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`external_id`),
                CONSTRAINT `fk.blue_media_gateway.logo_media_id` 
                    FOREIGN KEY (`logo_media_id`)
                        REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
