<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class InvalidTransactionException extends Exception
{
    private const MESSAGE = 'Order transaction not found for Order number: %s.';

    public function __construct(string $orderNumber)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $orderNumber)
        );
    }
}
