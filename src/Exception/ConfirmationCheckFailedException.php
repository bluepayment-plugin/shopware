<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

use function sprintf;

class ConfirmationCheckFailedException extends Exception
{
    private const MESSAGE = 'Blue Media confirmation check failed for Order %s / Transaction %s.';

    public function __construct(string $orderId, string $transactionId)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $orderId, $transactionId)
        );
    }
}
