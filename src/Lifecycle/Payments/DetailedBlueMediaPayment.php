<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\DetailedPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\DetailedPaymentHandler;

class DetailedBlueMediaPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Autopay Payment';
        $this->position = -15;
        $this->handlerIdentifier = DetailedPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = DetailedPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Autopay Payment',
                'description' => 'Detailed Autopay Online Payment',
            ],
            'de-DE' => [
                'name' => 'Autopay-Zahlung',
                'description' => 'Detaillierte Autopay Online-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Płatność Autopay',
                'description' => 'Szczegółowa płatność online Autopay',
            ],
        ];
    }
}
