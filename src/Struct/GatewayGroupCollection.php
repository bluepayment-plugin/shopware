<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Struct;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @method void add(GatewayGroupStruct $entity)
 * @method void set(string $key, GatewayGroupStruct $entity)
 * @method GatewayGroupStruct[] getIterator()
 * @method GatewayGroupStruct[] getElements()
 * @method null|GatewayGroupStruct get(string $key)
 * @method null|GatewayGroupStruct first()
 * @method null|GatewayGroupStruct last()
 */
class GatewayGroupCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return GatewayGroupStruct::class;
    }
}
