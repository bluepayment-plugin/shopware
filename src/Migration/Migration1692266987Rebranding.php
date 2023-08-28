<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1692266987Rebranding extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1692266987;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            "UPDATE payment_method_translation
                    SET name = REPLACE(name, 'Blue Media', 'Autopay'),
                        distinguishable_name = REPLACE(distinguishable_name, 'Blue Media', 'Autopay'),
                        description = REPLACE(description, 'Blue Media', 'Autopay')
                    WHERE
                        payment_method_id in (
                            SELECT pm.id FROM plugin p
                            INNER JOIN payment_method pm ON pm.plugin_id = p.id
                            WHERE p.name = 'BlueMediaShopwarePayment'
                        )
                    "
        );
        $connection->executeStatement(
            "UPDATE rule
                    SET name = REPLACE(name, 'Blue Media', 'Autopay'),
                        description = REPLACE(description, 'Blue Media', 'Autopay')
                    WHERE
                        id in (
                            0x429e9ed6bbd74bc7b2b667719ee636b9,
                            0x4abbdce88ca74b959f729ff183428736,
                            0xa869fc3430114e749abe380522a52ebd,
                            0x80b7d51305f642458a259b547642f8d7,
                            0x04f40d38b7c94643ba0aeab09b9b5f5f,
                            0x6b8b9c7dbe84455d96a94ae027d8a921,
                            0x15d71f7f6e1d413eaba433a9d9869be2,
                            0x753ccb48d0ce41f3aa7517c811f9cf6a
                        )
                    "
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
