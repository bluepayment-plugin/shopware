<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

use function sprintf;

class IntegrationNotEnabledException extends Exception
{
    private const MESSAGE = 'Blue Media Payment integration is not enabled for Sales Channel %s.';

    public function __construct(string $salesChannelId)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $salesChannelId)
        );
    }
}
