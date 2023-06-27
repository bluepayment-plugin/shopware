<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

class GatewayCurrencyDTO implements ResponseDTOInterface
{
    protected string $currency;

    protected ?float $minAmount = null;

    protected ?float $maxAmount = null;

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getMinAmount(): ?float
    {
        return $this->minAmount;
    }

    public function setMinAmount(?float $minAmount): void
    {
        $this->minAmount = $minAmount;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?float $maxAmount): void
    {
        $this->maxAmount = $maxAmount;
    }
}
