<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator\Error;

use Shopware\Core\Checkout\Cart\Error\Error;

class GatewayNotSelectedError extends Error
{
    private const KEY = 'blue-media-gateway-not-selected-error';

    private string $paymentMethodName;

    public function __construct(string $paymentMethodName)
    {
        $this->paymentMethodName = $paymentMethodName;
        $this->message = sprintf(
            'Please select gateway for Payment %s.',
            $paymentMethodName
        );

        parent::__construct($this->message);
    }

    public function getParameters(): array
    {
        return ['paymentMethodName' => $this->paymentMethodName];
    }

    public function getId(): string
    {
        return sprintf('%s-%s', self::KEY, $this->paymentMethodName);
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }

    public function getLevel(): int
    {
        return self::LEVEL_WARNING;
    }

    public function blockOrder(): bool
    {
        return true;
    }
}
