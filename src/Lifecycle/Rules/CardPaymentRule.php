<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class CardPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = 'a869fc3430114e749abe380522a52ebd';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Blue Media Card Payment [DO NOT EDIT]';
    }
}
