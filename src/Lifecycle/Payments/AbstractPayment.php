<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Common\SerializableTrait;
use JsonSerializable;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

abstract class AbstractPayment implements JsonSerializable
{
    use EntityIdTrait;
    use SerializableTrait;

    protected string $name;

    protected int $position;

    protected string $pluginId;

    protected string $mediaId;

    protected array $translations;

    protected ?string $availabilityRuleId;

    protected string $handlerIdentifier;

    protected bool $afterOrderEnabled;

    protected bool $active;

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandlerIdentifier(): string
    {
        return $this->handlerIdentifier;
    }

    public function getAvailabilityRuleId(): ?string
    {
        return $this->availabilityRuleId;
    }

    public function setAvailabilityRuleId(?string $availabilityRuleId): void
    {
        $this->availabilityRuleId = $availabilityRuleId;
    }

    public function setPluginId(string $id): void
    {
        $this->pluginId = $id;
    }

    public function setMediaId(string $id): void
    {
        $this->mediaId = $id;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
