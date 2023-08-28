<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class QuickTransferPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = '753ccb48d0ce41f3aa7517c811f9cf6a';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Autopay Quick Transfer Payment [DO NOT EDIT]';
    }
}
