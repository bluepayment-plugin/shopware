<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\CardPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\CardPaymentHandler;

class CardPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Card payment';
        $this->position = -7;
        $this->handlerIdentifier = CardPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = CardPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Card payment',
                'description' => 'Card Autopay Payment',
            ],
            'de-DE' => [
                'name' => 'Kartenzahlung',
                'description' => 'Karte Autopay Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Płatność kartą',
                'description' => 'Płatność kartą Autopay',
            ],
        ];
    }
}
