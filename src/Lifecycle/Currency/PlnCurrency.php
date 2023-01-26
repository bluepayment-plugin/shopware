<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Currency;

use BlueMedia\ShopwarePayment\Util\Constants;

class PlnCurrency extends AbstractCurrency
{
    public function __construct()
    {
        $this->isoCode = Constants::CURRENCY_PLN;
        $this->symbol = 'zł';
        $this->translations = [
            'en-GB' => [
                'name' => 'Polish Zloty',
                'shortName' => 'PLN',
            ],
            'de-DE' => [
                'name' => 'Polnischer Zloty',
                'shortName' => 'PLN',
            ],
            'pl-PL' => [
                'name' => 'Polski Złoty',
                'shortName' => 'PLN',
            ],
        ];
    }
}
