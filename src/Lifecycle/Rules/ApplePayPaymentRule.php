<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class ApplePayPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = '429e9ed6bbd74bc7b2b667719ee636b9';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Blue Media Apple Pay Payment [DO NOT EDIT]';
    }
}
