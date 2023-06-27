<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1675431204UpdateGatewayTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1675431204;
    }

    public function update(Connection $connection): void
    {
        $columnCount = $connection->executeQuery(
            'SHOW COLUMNS FROM `blue_media_gateway` WHERE Field = "external_id"'
        )->rowCount();
        if (empty($columnCount)) {
            return;
        }

        $connection->executeStatement(
            'ALTER TABLE `blue_media_gateway` CHANGE COLUMN 
                `external_id`
                `gateway_id`
                MEDIUMINT UNSIGNED NOT NULL
            ;'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
