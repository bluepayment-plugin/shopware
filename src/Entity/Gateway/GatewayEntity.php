<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class GatewayEntity extends Entity
{
    use EntityIdTrait;

    protected string $externalId;

    protected string $name;

    protected ?string $description = null;

    protected string $type;

    protected ?string $bankName = null;

    protected ?string $logoMediaId = null;

    protected ?MediaEntity $logoMedia = null;

    protected ?string $salesChannelId = null;

    protected ?SalesChannelEntity $salesChannel = null;

    protected ?GatewayCurrencyCollection $gatewayCurrencies = null;

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getLogoMediaId(): ?string
    {
        return $this->logoMediaId;
    }

    public function setLogoMediaId(?string $logoMediaId): void
    {
        $this->logoMediaId = $logoMediaId;
    }

    public function getLogoMedia(): ?MediaEntity
    {
        return $this->logoMedia;
    }

    public function setLogoMedia(?MediaEntity $logoMedia): void
    {
        $this->logoMedia = $logoMedia;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getGatewayCurrencies(): ?GatewayCurrencyCollection
    {
        return $this->gatewayCurrencies;
    }

    public function setGatewayCurrencies(?GatewayCurrencyCollection $gatewayCurrencies): void
    {
        $this->gatewayCurrencies = $gatewayCurrencies;
    }
}
