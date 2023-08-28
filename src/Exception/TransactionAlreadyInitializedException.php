<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class TransactionAlreadyInitializedException extends Exception
{
    private const MESSAGE = 'Autopay transaction for Transaction %s is already initialized.';

    public function __construct(string $transactionId)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $transactionId)
        );
    }
}
