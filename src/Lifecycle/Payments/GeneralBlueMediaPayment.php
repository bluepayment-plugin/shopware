<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\CurrencyPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;

class GeneralBlueMediaPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Blue Media Payment';
        $this->position = -10;
        $this->handlerIdentifier = GeneralPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = CurrencyPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Blue Media Payment',
                'description' => 'General Blue Media Online Payment',
            ],
            'de-DE' => [
                'name' => 'Blue Media-Zahlung',
                'description' => 'Allgemeine Blue Media Online-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Płatność Blue Media',
                'description' => 'Główna płatność online Blue Media',
            ],
        ];
    }
}
