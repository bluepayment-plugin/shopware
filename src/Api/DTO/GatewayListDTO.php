<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

class GatewayListDTO implements ResponseDTOInterface
{
    protected string $result;

    protected ?string $errorStatus = null;

    protected ?string $description = null;

    protected string $serviceID;

    protected string $messageID;

    protected GatewayDTOCollection $gatewayList;

    protected string $hash;

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getErrorStatus(): ?string
    {
        return $this->errorStatus;
    }

    public function setErrorStatus(?string $errorStatus): void
    {
        $this->errorStatus = $errorStatus;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getServiceID(): string
    {
        return $this->serviceID;
    }

    public function setServiceID(string $serviceID): void
    {
        $this->serviceID = $serviceID;
    }

    public function getMessageID(): string
    {
        return $this->messageID;
    }

    public function setMessageID(string $messageID): void
    {
        $this->messageID = $messageID;
    }

    public function getGatewayList(): GatewayDTOCollection
    {
        return $this->gatewayList;
    }

    public function setGatewayList(GatewayDTOCollection $gatewayList): void
    {
        $this->gatewayList = $gatewayList;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}
