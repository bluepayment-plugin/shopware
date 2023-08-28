<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class BackgroundTransactionFailedException extends Exception
{
    private const MESSAGE = 'Could not init background Autopay transaction for Transaction %s.';

    public function __construct(string $transactionId)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $transactionId)
        );
    }
}
