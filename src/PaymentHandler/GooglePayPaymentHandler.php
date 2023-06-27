<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\InvalidRequestParamsException;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class GooglePayPaymentHandler extends DetailedPaymentHandler
{
    public const REQUEST_PARAM = 'bmGooglePayPaymentToken';

    public const ALLOWED_AUTH_METHODS = ['PAN_ONLY', 'CRYPTOGRAM_3DS'];

    /**
     * 'AMEX', 'DISCOVER', 'JCB' - not supported currently by BM.
     */
    public const ALLOWED_CARD_NETWORKS = ['MASTERCARD', 'VISA'];

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
                    InitTransactionProcessor::PARAM_GATEWAY_ID => GatewayIds::GOOGLE_PAY,
                    InitTransactionProcessor::PARAM_PAYMENT_TOKEN =>  $this->getRequestPaymentToken($dataBag),
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
        return GatewayIds::GOOGLE_PAY === $gatewayEntity->getGatewayId();
    }

    public function gatewayGroupingSupported(): bool
    {
        return false;
    }

    public function isGatewayParamRequired(): bool
    {
        return false;
    }

    /**
     * @throws InvalidRequestParamsException
     */
    private function getRequestPaymentToken(RequestDataBag $dataBag)
    {
        $token = $dataBag->get(self::REQUEST_PARAM);

        if (null === $token) {
            throw new InvalidRequestParamsException('Invalid Google Token');
        }

        return base64_encode($token);
    }
}
