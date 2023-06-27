<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

use Shopware\Core\Framework\Rule\Container\AndRule;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\System\Currency\Rule\CurrencyRule;

class DetailedPaymentRule extends AbstractRule
{
    public const RULE_ID = '04f40d38b7c94643ba0aeab09b9b5f5f';

    public function __construct(array $currencyIds = [])
    {
        $this->id = static::RULE_ID;
        $this->name = 'Blue Media Detailed Payment [DO NOT EDIT]';
        $this->description = implode("\n\n", [
            // phpcs:disable
            "[English]\nBlue Media Payment blocking rule for unsupported currencies and payment gateways. It is created automatically during plugin installation. DO NOT EDIT OR DELETE this rule!",
            "[Deutsch]\nBlue Media Payment-Blockierungsregel für nicht unterstützte Währungen und Zahlungs-Gateways. Es wird automatisch während der Plugin-Installation erstellt. BEARBEITEN ODER LÖSCHEN SIE diese Regel NICHT!",
            "[Polski]\nReguła blokująca Blue Media Payment dla nieobsługiwanych walut oraz bramek płatności. Jest tworzona automatycznie podczas instalacji wtyczki. NIE EDYTUJ ANI NIE USUWAJ tej reguły!",
            // phpcs:enable
        ]);
        $this->priority = 110;
        $this->moduleTypes = ['types' => ['payment']];
        $this->conditions = [
            [
                'type' => (new AndRule())->getName(),
                'children' => [
                    [
                        'type' => (new CurrencyRule())->getName(),
                        'value' => [
                            'currencyIds' => $currencyIds,
                            'operator' => Rule::OPERATOR_EQ,
                        ],
                    ],
                ],
            ],
        ];
    }
}
