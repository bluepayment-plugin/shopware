<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

use Shopware\Core\Framework\Rule\Container\AndRule;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\System\Currency\Rule\CurrencyRule;

class CurrencyPaymentRule extends AbstractRule
{
    public const RULE_ID = '80b7d51305f642458a259b547642f8d7';

    public function __construct(array $currencyIds = [])
    {
        $this->id = self::RULE_ID;
        $this->name = 'Blue Media Payment Currency [DO NOT EDIT]';
        $this->description = implode("\n\n", [
            // phpcs:disable
            "[English]\nA rule that blocks Blue Media Payment for not supported currencies. It is created automatically on the plugin install. DO NOT EDIT OR REMOVE this rule!",
            "[Deutsch]\nEine Regel, die Blue Media Payment für nicht unterstützte Währungen blockiert. Es wird automatisch bei der Plugin-Installation erstellt. Diese Regel NICHT BEARBEITEN ODER ENTFERNEN!",
            "[Polski]\nReguła blokująca Blue Media Payment dla nieobsługiwanych walut. Jest tworzona automatycznie podczas instalacji wtyczki. NIE EDYTUJ ANI NIE USUWAJ tej reguły!",
            // phpcs:enable
        ]);
        $this->priority = 100;
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
