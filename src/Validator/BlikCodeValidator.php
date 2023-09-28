<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator;

use BlueMedia\ShopwarePayment\Exception\BlikCodeNotProvidedException;
use BlueMedia\ShopwarePayment\Exception\InvalidBlikCodeException;

class BlikCodeValidator
{
    public const BLIK_CODE_REGEX = '/^\d{6}$/';

    /**
     * @throws InvalidBlikCodeException
     * @throws BlikCodeNotProvidedException
     */
    public function validate(?string $blikCode): void
    {
        if (empty($blikCode)) {
            throw new InvalidBlikCodeException();
        }

        if (false === preg_match(self::BLIK_CODE_REGEX, $blikCode)) {
            throw new BlikCodeNotProvidedException();
        }
    }
}
