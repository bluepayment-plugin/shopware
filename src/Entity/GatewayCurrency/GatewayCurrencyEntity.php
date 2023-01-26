<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Entity\GatewayCurrency;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Currency\CurrencyEntity;

class GatewayCurrencyEntity extends Entity
{
    use EntityIdTrait;

    protected string $currencyId;

    protected ?CurrencyEntity $currency = null;

    protected string $gatewayId;

    protected ?GatewayEntity $gateway = null;

    protected ?float $minCartAmount = null;

    protected ?float $maxCartAmount = null;

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getCurrency(): ?CurrencyEntity
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyEntity $currency): void
    {
        $this->currency = $currency;
    }

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

    public function getMinCartAmount(): ?float
    {
        return $this->minCartAmount;
    }

    public function setMinCartAmount(?float $minCartAmount): void
    {
        $this->minCartAmount = $minCartAmount;
    }

    public function getMaxCartAmount(): ?float
    {
        return $this->maxCartAmount;
    }

    public function setMaxCartAmount(?float $maxCartAmount): void
    {
        $this->maxCartAmount = $maxCartAmount;
    }
}
