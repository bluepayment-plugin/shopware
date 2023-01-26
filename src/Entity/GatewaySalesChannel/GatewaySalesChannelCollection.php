<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewaySalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GatewaySalesChannelEntity $entity)
 * @method void set(string $key, GatewaySalesChannelEntity $entity)
 * @method GatewaySalesChannelEntity[] getIterator()
 * @method GatewaySalesChannelEntity[] getElements()
 * @method GatewaySalesChannelEntity|null get(string $key)
 * @method GatewaySalesChannelEntity|null first()
 * @method GatewaySalesChannelEntity|null last()
 */
class GatewaySalesChannelCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GatewaySalesChannelEntity::class;
    }
}
