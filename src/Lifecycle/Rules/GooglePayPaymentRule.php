<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class GooglePayPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = '6b8b9c7dbe84455d96a94ae027d8a921';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Autopay Google Pay Payment [DO NOT EDIT]';
    }
}
