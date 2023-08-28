<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;

class GeneralBlueMediaPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Autopay Payment';
        $this->position = -10;
        $this->handlerIdentifier = GeneralPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = null;
        $this->translations = [
            'en-GB' => [
                'name' => 'Autopay Payment',
                'description' => 'General Autopay Online Payment',
            ],
            'de-DE' => [
                'name' => 'Autopay-Zahlung',
                'description' => 'Allgemeine Autopay Online-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Płatność Autopay',
                'description' => 'Główna płatność online Autopay',
            ],
        ];
    }
}
