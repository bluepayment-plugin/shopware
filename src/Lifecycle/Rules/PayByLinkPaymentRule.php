<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class PayByLinkPaymentRule extends DetailedPaymentRule
{
    public const RULE_ID = '15d71f7f6e1d413eaba433a9d9869be2';

    public function __construct(array $currencyIds = [])
    {
        parent::__construct($currencyIds);
        $this->name = 'Autopay PayByLink Payment [DO NOT EDIT]';
    }
}
