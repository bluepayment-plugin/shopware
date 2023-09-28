<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class BlikCodeNotProvidedException extends Exception
{
    private const MESSAGE = 'BLIK code not provided.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
