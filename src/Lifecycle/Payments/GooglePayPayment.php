<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\GooglePayPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\GooglePayPaymentHandler;

class GooglePayPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Google Pay';
        $this->position = -7;
        $this->handlerIdentifier = GooglePayPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = GooglePayPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Google Pay',
                'description' => 'Google Pay Blue Media Payment',
            ],
            'de-DE' => [
                'name' => 'Google Pay',
                'description' => 'Google Pay Blue Media-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Google Pay',
                'description' => 'Płatność Google Pay Blue Media',
            ],
        ];
    }
}
