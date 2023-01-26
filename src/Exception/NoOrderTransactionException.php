<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class NoOrderTransactionException extends Exception
{
    public function __construct()
    {
        parent::__construct('No order on transaction');
    }
}
