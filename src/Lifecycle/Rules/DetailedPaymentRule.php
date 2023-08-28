<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

class DetailedPaymentRule extends AbstractRule
{
    public const RULE_ID = '04f40d38b7c94643ba0aeab09b9b5f5f';

    public function __construct()
    {
        $this->id = static::RULE_ID;
        $this->name = 'Autopay Detailed Payment [DO NOT EDIT]';
        $this->description = implode("\n\n", [
            // phpcs:disable
            "[English]\nAutopay Payment blocking rule for unsupported currencies and payment gateways. It is created automatically during plugin installation. DO NOT EDIT OR DELETE this rule!",
            "[Deutsch]\nAutopay Payment-Blockierungsregel für nicht unterstützte Währungen und Zahlungs-Gateways. Es wird automatisch während der Plugin-Installation erstellt. BEARBEITEN ODER LÖSCHEN SIE diese Regel NICHT!",
            "[Polski]\nReguła blokująca Autopay Payment dla nieobsługiwanych walut oraz bramek płatności. Jest tworzona automatycznie podczas instalacji wtyczki. NIE EDYTUJ ANI NIE USUWAJ tej reguły!",
            // phpcs:enable
        ]);
        $this->priority = 110;
        $this->moduleTypes = ['types' => ['payment']];
        $this->conditions = null;
    }
}
