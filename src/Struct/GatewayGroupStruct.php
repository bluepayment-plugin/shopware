<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Struct;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayCollection;
use Shopware\Core\Framework\Struct\Struct;

class GatewayGroupStruct extends Struct
{
    protected string $type;

    protected GatewayCollection $gateways;

    public function __construct(
        string $type,
        GatewayCollection $gateways
    ) {
        $this->type = $type;
        $this->gateways = $gateways;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getGateways(): GatewayCollection
    {
        return $this->gateways;
    }

    public function setGateways(GatewayCollection $gateways): void
    {
        $this->gateways = $gateways;
    }
}
