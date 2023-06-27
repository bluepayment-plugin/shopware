<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ApplePaySupported extends Constraint
{
    public const REQUEST_PARAM = 'blueMediaApplePaySupported';

    public const IS_BLUE_MEDIA_APPLE_PAY_NOT_SUPPORTED = 'c6e8617f-e0a7-425b-92a1-edd8152471fd';

    protected static $errorNames = [
        self::IS_BLUE_MEDIA_APPLE_PAY_NOT_SUPPORTED => 'IS_BLUE_MEDIA_APPLE_PAY_NOT_SUPPORTED',
    ];

    public string $message = 'Apple Pay is not supported for this device.';
}
