<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

use function sprintf;

class InvalidOrderException extends Exception
{
    private const MESSAGE = 'The order with order number %s is invalid or could not be found.';
    private const NOT_AVAILABLE = 'N/A';

    public function __construct(string $orderNumber = self::NOT_AVAILABLE)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $orderNumber)
        );
    }
}
