<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;

/**
 * Used to determine Blue Media Payment Handlers in PaymentHandlerResolver
 *
 * @see \BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver
 */
interface BlueMediaPaymentHandlerInterface extends AsynchronousPaymentHandlerInterface
{
}
