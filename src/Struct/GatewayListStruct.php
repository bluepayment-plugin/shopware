<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Struct;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayCollection;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Uuid\Uuid;

class GatewayListStruct extends Struct
{
    protected GatewayCollection $gateways;

    protected GatewayGroupCollection $gatewayGroups;

    protected ?string $selectedGatewayId;

    private ?string $handlerIdentifier;

    public function __construct(
        ?GatewayGroupCollection $gatewayGroups = null,
        ?GatewayCollection $gateways = null,
        ?string $handlerIdentifier = null,
        ?string $selectedGatewayId = null
    ) {
        $this->gatewayGroups = null === $gatewayGroups ? new GatewayGroupCollection() : $gatewayGroups;
        $this->gateways = null === $gateways ? new GatewayCollection() : $gateways;
        $this->selectedGatewayId = $selectedGatewayId;
        $this->handlerIdentifier = $handlerIdentifier;
    }

    public function getGatewayGroups(): GatewayGroupCollection
    {
        return $this->gatewayGroups;
    }

    public function setGatewayGroups(GatewayGroupCollection $gatewayGroups): void
    {
        $this->gatewayGroups = $gatewayGroups;
    }

    public function hasSelectedGatewayId(): bool
    {
        return Uuid::isValid((string) $this->selectedGatewayId);
    }

    public function getSelectedGatewayId(): ?string
    {
        return $this->selectedGatewayId;
    }

    public function setSelectedGatewayId(?string $selectedGatewayId): void
    {
        $this->selectedGatewayId = $selectedGatewayId;
    }

    public function getGateways(): GatewayCollection
    {
        return $this->gateways;
    }

    public function setGateways(GatewayCollection $gateways): void
    {
        $this->gateways = $gateways;
    }

    public function getHandlerIdentifier(): ?string
    {
        return $this->handlerIdentifier;
    }

    public function setHandlerIdentifier(?string $handlerIdentifier): void
    {
        $this->handlerIdentifier = $handlerIdentifier;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->gatewayGroups->count() && 0 === $this->gateways->count();
    }
}
