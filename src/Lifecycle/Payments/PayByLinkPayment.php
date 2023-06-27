<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Payments;

use BlueMedia\ShopwarePayment\Lifecycle\Rules\PayByLinkPaymentRule;
use BlueMedia\ShopwarePayment\PaymentHandler\PayByLinkPaymentHandler;

class PayByLinkPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->name = 'Online bank transfer';
        $this->position = -9;
        $this->handlerIdentifier = PayByLinkPaymentHandler::class;
        $this->afterOrderEnabled = true;
        $this->availabilityRuleId = PayByLinkPaymentRule::RULE_ID;
        $this->translations = [
            'en-GB' => [
                'name' => 'Online bank transfer',
                'description' => 'PayByLink Blue Media Payment',
            ],
            'de-DE' => [
                'name' => 'Online-Banküberweisung',
                'description' => 'PayByLink Blue Media-Zahlung',
            ],
            'pl-PL' => [
                'name' => 'Przelew internetowy',
                'description' => 'Płatność PayByLink Blue Media',
            ],
        ];
    }
}
