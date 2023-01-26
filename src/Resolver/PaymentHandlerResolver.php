<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Resolver;

use BlueMedia\ShopwarePayment\PaymentHandler\BlueMediaPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;

use function is_a;

class PaymentHandlerResolver
{
    /**
     * @var iterable<BlueMediaPaymentHandlerInterface>
     */
    private iterable $paymentHandlers;

    public function __construct(iterable $paymentHandlers)
    {
        $this->paymentHandlers = $paymentHandlers;
    }

    public function resolve(PaymentMethodEntity $paymentMethod): ?BlueMediaPaymentHandlerInterface
    {
        foreach ($this->paymentHandlers as $paymentHandler) {
            if (is_a($paymentHandler, $paymentMethod->getHandlerIdentifier())) {
                return $paymentHandler;
            }
        }

        return null;
    }
}
