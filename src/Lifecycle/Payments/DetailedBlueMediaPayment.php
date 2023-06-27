<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\DetailedPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\DetailedPaymentHandler;

class DetailedBlueMediaPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Blue Media Payment';
        $this->position = -15;
        $this->handlerIdentifier = DetailedPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = DetailedPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Blue Media Payment',
                'description' => 'Detailed Blue Media Online Payment',
            ],
            'de-DE' => [
                'name' => 'Blue Media-Zahlung',
                'description' => 'Detaillierte Blue Media Online-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Płatność Blue Media',
                'description' => 'Szczegółowa płatność online Blue Media',
            ],
        ];
    }
}
