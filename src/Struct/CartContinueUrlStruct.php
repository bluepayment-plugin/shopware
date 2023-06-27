<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Struct;

use Shopware\Core\Framework\Struct\Struct;

class CartContinueUrlStruct extends Struct
{
    private string $transactionContinueRedirect;

    private string $checkoutErrorUrl;

    public function __construct(
        string $transactionContinueRedirect,
        string $checkoutErrorUrl
    ) {
        $this->transactionContinueRedirect = $transactionContinueRedirect;
        $this->checkoutErrorUrl = $checkoutErrorUrl;
    }

    public function getTransactionContinueRedirect(): string
    {
        return $this->transactionContinueRedirect;
    }

    public function getCheckoutErrorUrl(): string
    {
        return $this->checkoutErrorUrl;
    }
}
