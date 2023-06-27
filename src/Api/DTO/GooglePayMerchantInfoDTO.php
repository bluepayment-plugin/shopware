<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\DTO;

class GooglePayMerchantInfoDTO implements ResponseDTOInterface
{
    protected string $result;

    protected string $authJwt;

    protected string $merchantId;

    protected string $merchantOrigin;

    protected string $merchantName;

    protected int $acceptorId;

    protected string $hash;

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getAuthJwt(): string
    {
        return $this->authJwt;
    }

    public function setAuthJwt(string $authJwt): void
    {
        $this->authJwt = $authJwt;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function getMerchantOrigin(): string
    {
        return $this->merchantOrigin;
    }

    public function setMerchantOrigin(string $merchantOrigin): void
    {
        $this->merchantOrigin = $merchantOrigin;
    }

    public function getMerchantName(): string
    {
        return $this->merchantName;
    }

    public function setMerchantName(string $merchantName): void
    {
        $this->merchantName = $merchantName;
    }

    public function getAcceptorId(): int
    {
        return $this->acceptorId;
    }

    public function setAcceptorId(int $acceptorId): void
    {
        $this->acceptorId = $acceptorId;
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
