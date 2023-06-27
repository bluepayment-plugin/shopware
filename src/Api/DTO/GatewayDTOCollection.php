<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @method void add(GatewayDTO $entity)
 * @method void set(string $key, GatewayDTO $entity)
 * @method GatewayDTO[] getIterator()
 * @method GatewayDTO[] getElements()
 * @method null|GatewayDTO get(string $key)
 * @method null|GatewayDTO first()
 * @method null|GatewayDTO last()
 */
class GatewayDTOCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return GatewayDTO::class;
    }
}
