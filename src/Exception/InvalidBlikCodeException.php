<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class InvalidBlikCodeException extends Exception
{
    private const MESSAGE = 'Invalid BLIK code expression.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
