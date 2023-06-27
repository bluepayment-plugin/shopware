<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\ApplePayPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\ApplePayPaymentHandler;

class ApplePayPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Apple Pay';
        $this->position = -7;
        $this->handlerIdentifier = ApplePayPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = ApplePayPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Apple Pay',
                'description' => 'Apple Pay Blue Media Payment',
            ],
            'de-DE' => [
                'name' => 'Apple Pay',
                'description' => 'Apple Pay Blue Media-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Apple Pay',
                'description' => 'Płatność Apple Pay Blue Media',
            ],
        ];
    }
}
