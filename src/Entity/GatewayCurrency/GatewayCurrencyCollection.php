<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewayCurrency;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GatewayCurrencyEntity $entity)
 * @method void set(string $key, GatewayCurrencyEntity $entity)
 * @method GatewayCurrencyEntity[] getIterator()
 * @method GatewayCurrencyEntity[] getElements()
 * @method GatewayCurrencyEntity|null get(string $key)
 * @method GatewayCurrencyEntity|null first()
 * @method GatewayCurrencyEntity|null last()
 */
class GatewayCurrencyCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GatewayCurrencyEntity::class;
    }
}
