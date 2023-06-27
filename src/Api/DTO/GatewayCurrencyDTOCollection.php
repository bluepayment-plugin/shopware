<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @method void add(GatewayCurrencyDTO $entity)
 * @method void set(string $key, GatewayCurrencyDTO $entity)
 * @method GatewayCurrencyDTO[] getIterator()
 * @method GatewayCurrencyDTO[] getElements()
 * @method null|GatewayCurrencyDTO get(string $key)
 * @method null|GatewayCurrencyDTO first()
 * @method null|GatewayCurrencyDTO last()
 */
class GatewayCurrencyDTOCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return GatewayCurrencyDTO::class;
    }
}
