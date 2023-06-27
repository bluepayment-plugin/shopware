<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Util\GatewayTypes;

class PayByLinkPaymentHandler extends DetailedPaymentHandler
{
    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        return $gatewayEntity->getType(true) === GatewayTypes::PBL;
    }

    public function gatewayGroupingSupported(): bool
    {
        return false;
    }
}
