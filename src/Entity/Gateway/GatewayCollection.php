<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GatewayEntity $entity)
 * @method void set(string $key, GatewayEntity $entity)
 * @method GatewayEntity[] getIterator()
 * @method GatewayEntity[] getElements()
 * @method GatewayEntity|null get(string $key)
 * @method GatewayEntity|null first()
 * @method GatewayEntity|null last()
 */
class GatewayCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GatewayEntity::class;
    }
}
