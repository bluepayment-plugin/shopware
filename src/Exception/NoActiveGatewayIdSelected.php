<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class NoActiveGatewayIdSelected extends Exception
{
    public function __construct()
    {
        parent::__construct('No active Autopay Gateway selected for Payment');
    }
}
