<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class BlikPaymentHandler extends DetailedPaymentHandler
{
    /**
     * @throws AsyncPaymentProcessException
     */
    public function pay(
        AsyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): RedirectResponse {
        try {
            $response = $this->initTransactionProcessor->process(
                $transaction,
                $salesChannelContext,
                [
                    InitTransactionProcessor::PARAM_GATEWAY_ID => GatewayIds::BLIK,
                ]
            );
        } catch (Throwable $e) {
            throw new AsyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }

        return new RedirectResponse($response->getRedirectUrl());
    }

    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        return GatewayIds::BLIK === $gatewayEntity->getGatewayId();
    }

    public function gatewayGroupingSupported(): bool
    {
        return false;
    }

    public function isGatewayParamRequired(): bool
    {
        return false;
    }
}
