<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Throwable;

class BlikPaymentHandler extends CardPaymentHandler
{
    public function pay(
        SyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $transactionParams = [
                InitTransactionProcessor::PARAM_GATEWAY_ID => GatewayIds::BLIK,
            ];

            if ($dataBag->has('blikCode')) {
                $transactionParams[InitTransactionProcessor::PARAM_AUTHORIZATION_CODE]
                    = $dataBag->getDigits('blikCode');
            }

            $this->initTransactionProcessor->processInit(
                $transaction,
                $salesChannelContext,
                $transactionParams
            );
        } catch (Throwable $e) {
            throw new AsyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }
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
