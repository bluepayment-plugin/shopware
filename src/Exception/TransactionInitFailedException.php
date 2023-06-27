<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Exception;

use Exception;

class TransactionInitFailedException extends Exception
{
    public const REASON_UNKNOWN = 'INTERNAL_UNKNOWN';

    private const MESSAGE = 'Could not init Blue Media transaction for Transaction %s. Reason: %s';

    public function __construct(string $transactionId, ?string $reason = null)
    {
        if (null === $reason) {
            $reason = self::REASON_UNKNOWN;
        }

        parent::__construct(
            sprintf(self::MESSAGE, $transactionId, $reason)
        );
    }
}
