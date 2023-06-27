<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Icons;

use BlueMedia\ShopwarePayment\PaymentHandler\ApplePayPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\BlikPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\CardPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\DetailedPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\GooglePayPaymentHandler;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;

class IconsFactory
{
    public function createFromPayment(PaymentMethodEntity $payment): ?AbstractPaymentIcon
    {
        switch ($payment->getHandlerIdentifier()) {
            case DetailedPaymentHandler::class:
            case GeneralPaymentHandler::class:
                return new DefaultPaymentIcon();
            case ApplePayPaymentHandler::class:
                return new ApplePayPaymentIcon();
            case CardPaymentHandler::class:
                return new CardPaymentIcon();
            case BlikPaymentHandler::class:
                return new BlikPaymentIcon();
            case GooglePayPaymentHandler::class:
                return new GooglePayPaymentIcon();
            default:
                return null;
        }
    }
}
