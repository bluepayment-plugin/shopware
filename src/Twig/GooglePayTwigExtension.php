<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\PaymentHandler\GooglePayPaymentHandler;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GooglePayTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isBlueMediaGooglePayPayment', [$this, 'isBlueMediaGooglePayPayment']),
        ];
    }

    public function isBlueMediaGooglePayPayment(PaymentMethodEntity $paymentMethod): bool
    {
        return GooglePayPaymentHandler::class === $paymentMethod->getHandlerIdentifier();
    }
}
