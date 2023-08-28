<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class BlikPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = '4abbdce88ca74b959f729ff183428736';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Autopay BLIK Payment [DO NOT EDIT]';
    }
}
