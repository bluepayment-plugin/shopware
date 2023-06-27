<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Resolver;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\PaymentHandler\BlueMediaPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;

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
            if (get_class($paymentHandler) === $paymentMethod->getHandlerIdentifier()) {
                return $paymentHandler;
            }
        }

        return null;
    }

    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        foreach ($this->paymentHandlers as $paymentHandler) {
            if ($paymentHandler->isGatewaySupported($gatewayEntity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable<BlueMediaPaymentHandlerInterface>
     */
    public function getHandlers(): iterable
    {
        return $this->paymentHandlers;
    }

    /**
     * @return string[]
     */
    public function getGatewayRequiredHandlersNames(): array
    {
        $handlersClassesNames = [];
        foreach ($this->paymentHandlers as $paymentHandler) {
            if ($paymentHandler->isGatewayParamRequired()) {
                $handlersClassesNames[] = get_class($paymentHandler);
            }
        }
        return $handlersClassesNames;
    }
}
