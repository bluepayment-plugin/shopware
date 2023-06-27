<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\Gateway;

use BlueMedia\ShopwarePayment\Entity\GatewayCurrency\GatewayCurrencyCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;

use function Symfony\Component\String\u;

class GatewayEntity extends Entity
{
    use EntityIdTrait;

    protected int $gatewayId;

    protected string $name;

    protected ?string $description = null;

    protected string $type;

    protected ?string $bankName = null;

    protected ?string $logoMediaId = null;

    protected ?bool $isSupported = null;

    protected ?MediaEntity $logoMedia = null;

    protected ?GatewayCurrencyCollection $currencies = null;

    protected ?SalesChannelCollection $salesChannelsEnabled = null;

    protected ?SalesChannelCollection $salesChannelsActive = null;

    public function getGatewayId(): int
    {
        return $this->gatewayId;
    }

    public function setGatewayId(int $gatewayId): void
    {
        $this->gatewayId = $gatewayId;
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

    public function getType(bool $normalize = false): string
    {
        if ($normalize) {
            return u($this->type)->ascii()->lower()->replace(' ', '-')->toString();
        }

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

    public function getCurrencies(): ?GatewayCurrencyCollection
    {
        return $this->currencies;
    }

    public function setCurrencies(?GatewayCurrencyCollection $currencies): void
    {
        $this->currencies = $currencies;
    }

    public function getSalesChannelsEnabled(): ?SalesChannelCollection
    {
        return $this->salesChannelsEnabled;
    }

    public function setSalesChannelsEnabled(SalesChannelCollection $salesChannelsEnabled): void
    {
        $this->salesChannelsEnabled = $salesChannelsEnabled;
    }

    public function getSalesChannelsActive(): ?SalesChannelCollection
    {
        return $this->salesChannelsActive;
    }

    public function setSalesChannelsActive(SalesChannelCollection $salesChannelsActive): void
    {
        $this->salesChannelsActive = $salesChannelsActive;
    }

    public function isSupported(): ?bool
    {
        return $this->isSupported;
    }

    public function setIsSupported(?bool $isSupported): void
    {
        $this->isSupported = $isSupported;
    }
}
