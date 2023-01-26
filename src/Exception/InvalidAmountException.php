<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class InvalidAmountException extends Exception
{
    public function __construct()
    {
        parent::__construct('Amount or currency invalid');
    }
}
