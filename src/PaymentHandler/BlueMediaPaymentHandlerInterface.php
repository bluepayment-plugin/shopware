<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;

/**
 * Used to determine Blue Media Payment Handlers in PaymentHandlerResolver
 *
 * @see \BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver
 */
interface BlueMediaPaymentHandlerInterface
{
    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool;

    public function gatewayGroupingSupported(): bool;

    public function isGatewayParamRequired(): bool;
}
