<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Util;

class GatewayTypes
{
    public const E_WALLET = 'portfel-elektroniczny';
    public const FAST_TRANSFER = 'szybki-przelew';
    public const INSTALLMENTS = 'raty-online';
    public const AUTOMATIC_PAYMENT = 'platnosc-automatyczna';
    public const PBL = 'pbl';
    public const BLIK = 'blik';
    public const CARD = 'karta-platnicza';

    public const GROUPING_TRANSFER = 'transfers';
}
