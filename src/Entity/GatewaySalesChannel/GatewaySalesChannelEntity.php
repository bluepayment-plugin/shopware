<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewaySalesChannel;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class GatewaySalesChannelEntity extends Entity
{
    use EntityIdTrait;

    protected string $gatewayId;

    protected ?GatewayEntity $gateway = null;

    protected string $salesChannelId;

    protected ?SalesChannelEntity $salesChannel = null;

    protected bool $active;

    public function getGatewayId(): string
    {
        return $this->gatewayId;
    }

    public function setGatewayId(string $gatewayId): void
    {
        $this->gatewayId = $gatewayId;
    }

    public function getGateway(): ?GatewayEntity
    {
        return $this->gateway;
    }

    public function setGateway(?GatewayEntity $gateway): void
    {
        $this->gateway = $gateway;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
