<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\BlikPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\BlikPaymentHandler;

class BlikPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'BLIK';
        $this->position = -7;
        $this->handlerIdentifier = BlikPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = BlikPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'BLIK',
                'description' => 'BLIK Blue Media Payment',
            ],
            'de-DE' => [
                'name' => 'BLIK',
                'description' => 'BLIK Blue Media-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'BLIK',
                'description' => 'Płatność BLIK Blue Media',
            ],
        ];
    }
}
