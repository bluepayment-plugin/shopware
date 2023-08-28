<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\QuickTransferPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\QuickTransferPaymentHandler;

class QuickTransferPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Quick transfer';
        $this->position = -8;
        $this->handlerIdentifier = QuickTransferPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = QuickTransferPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Quick transfer',
                'description' => 'Quick transfer Autopay Payment',
            ],
            'de-DE' => [
                'name' => 'Schnelle Übertragung',
                'description' => 'Schnelle Überweisung Autopay-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Szybki przelew',
                'description' => 'Płatność Szybki przelew Autopay',
            ],
        ];
    }
}
