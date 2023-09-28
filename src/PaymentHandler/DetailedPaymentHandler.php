<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\ConfirmationCheckFailedException;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use BlueMedia\ShopwarePayment\Util\GatewayTypes;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentFinalizeException;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class DetailedPaymentHandler implements BlueMediaPaymentHandlerInterface, AsynchronousPaymentHandlerInterface
{
    protected ClientFactory $clientFactory;

    protected InitTransactionProcessor $initTransactionProcessor;

    protected SalesChannelContextDataProvider $channelContextDataProvider;

    public function __construct(
        ClientFactory $clientFactory,
        InitTransactionProcessor $initTransactionProcessor,
        SalesChannelContextDataProvider $channelContextDataProvider
    ) {
        $this->clientFactory = $clientFactory;
        $this->initTransactionProcessor = $initTransactionProcessor;
        $this->channelContextDataProvider = $channelContextDataProvider;
    }

    /**
     * @throws AsyncPaymentProcessException
     */
    public function pay(
        AsyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): RedirectResponse {
        try {
            $gatewayEntity = $this->channelContextDataProvider->getSelectedGateway(
                $salesChannelContext,
                $transaction->getOrderTransaction()->getAmount()->getTotalPrice()
            );
            $response = $this->initTransactionProcessor->processContinue(
                $transaction,
                $salesChannelContext,
                [InitTransactionProcessor::PARAM_GATEWAY_ID => $gatewayEntity->getGatewayId()]
            );
        } catch (Throwable $e) {
            throw new AsyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }

        return new RedirectResponse($response->getRedirectUrl());
    }

    /**
     * @throws AsyncPaymentFinalizeException
     */
    public function finalize(
        AsyncPaymentTransactionStruct $transaction,
        Request $request,
        SalesChannelContext $salesChannelContext
    ): void {
        try {
            $client = $this->clientFactory->createFromPluginConfig($salesChannelContext->getSalesChannelId());

            $valid = $client->doConfirmationCheck($request->query->all());
            if (false === $valid) {
                throw new ConfirmationCheckFailedException(
                    $transaction->getOrder()->getId(),
                    $transaction->getOrderTransaction()->getId()
                );
            }
        } catch (Throwable $e) {
            throw new AsyncPaymentFinalizeException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }
    }

    public function isGatewaySupported(GatewayEntity $gatewayEntity): bool
    {
        return in_array(
            $gatewayEntity->getGatewayId(),
            [GatewayIds::GENERAL_CREDIT_CARD, GatewayIds::GOOGLE_PAY, GatewayIds::APPLE_PAY, GatewayIds::BLIK],
            true
        )
        || in_array($gatewayEntity->getType(true), [GatewayTypes::PBL, GatewayTypes::FAST_TRANSFER], true);
    }

    public function gatewayGroupingSupported(): bool
    {
        return true;
    }

    public function isGatewayParamRequired(): bool
    {
        return true;
    }
}
