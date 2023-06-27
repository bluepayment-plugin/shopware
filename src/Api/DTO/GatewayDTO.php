<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

class GatewayDTO implements ResponseDTOInterface
{
    public const STATE_OK = 'OK';
    public const STATE_TEMPORARY_DISABLED = 'TEMPORARY_DISABLED';
    public const STATE_DISABLED = 'DISABLED';

    protected int $gatewayID;

    protected string $gatewayName;

    protected string $gatewayType;

    protected string $bankName;

    protected string $iconURL;

    protected string $state;

    protected string $stateDate;

    protected ?string $gatewayDescription;

    protected bool $inBalanceAllowed;

    protected GatewayCurrencyDTOCollection $currencyList;

    public function getGatewayID(): int
    {
        return $this->gatewayID;
    }

    public function setGatewayID(int $gatewayID): void
    {
        $this->gatewayID = $gatewayID;
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(string $gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }

    public function getGatewayType(): string
    {
        return $this->gatewayType;
    }

    public function setGatewayType(string $gatewayType): void
    {
        $this->gatewayType = $gatewayType;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getIconURL(): string
    {
        return $this->iconURL;
    }

    public function setIconURL(string $iconURL): void
    {
        $this->iconURL = $iconURL;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getStateDate(): string
    {
        return $this->stateDate;
    }

    public function setStateDate(string $stateDate): void
    {
        $this->stateDate = $stateDate;
    }

    public function getGatewayDescription(): ?string
    {
        return $this->gatewayDescription;
    }

    public function setGatewayDescription(?string $gatewayDescription): void
    {
        $this->gatewayDescription = $gatewayDescription;
    }

    public function isInBalanceAllowed(): bool
    {
        return $this->inBalanceAllowed;
    }

    public function setInBalanceAllowed(bool $inBalanceAllowed): void
    {
        $this->inBalanceAllowed = $inBalanceAllowed;
    }

    public function getCurrencyList(): GatewayCurrencyDTOCollection
    {
        return $this->currencyList;
    }

    public function setCurrencyList(GatewayCurrencyDTOCollection $currencyList): void
    {
        $this->currencyList = $currencyList;
    }
}
