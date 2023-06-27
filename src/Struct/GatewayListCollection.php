<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Struct;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @method void add(GatewayListStruct $entity)
 * @method void set(string $key, GatewayListStruct $entity)
 * @method GatewayListStruct[] getIterator()
 * @method GatewayListStruct[] getElements()
 * @method null|GatewayListStruct get(string $key)
 * @method null|GatewayListStruct first()
 * @method null|GatewayListStruct last()
 */
class GatewayListCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return GatewayListStruct::class;
    }
}
