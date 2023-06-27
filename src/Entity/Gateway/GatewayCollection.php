<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GatewayEntity $entity)
 * @method void set(string $key, GatewayEntity $entity)
 * @method GatewayEntity[] getIterator()
 * @method GatewayEntity[] getElements()
 * @method null|GatewayEntity get(string $key)
 * @method null|GatewayEntity first()
 * @method null|GatewayEntity last()
 */
class GatewayCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GatewayEntity::class;
    }

    /**
     * @return GatewayCollection
     */
    public function reduceToSupported(): EntityCollection
    {
        return $this->filterAndReduceByProperty('isSupported', false);
    }

    public function filterByType($type = true, $normalize = false): self
    {
        return $this->filter(fn(GatewayEntity $entity) => $type === $entity->getType($normalize));
    }

    /**
     * @return MediaEntity[]
     */
    public function getLogos(): array
    {
        return array_filter($this->map(fn(GatewayEntity $entity) => $entity->getLogoMedia()));
    }
}
