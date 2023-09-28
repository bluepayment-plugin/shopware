<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\ConfirmationCheckFailedException;
use BlueMedia\ShopwarePayment\Processor\InitTransactionProcessor;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentFinalizeException;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class GeneralPaymentHandler implements BlueMediaPaymentHandlerInterface, AsynchronousPaymentHandlerInterface
{
    private ClientFactory $clientFactory;

    private InitTransactionProcessor $initTransactionProcessor;

    public function __construct(
        ClientFactory $clientFactory,
        InitTransactionProcessor $initTransactionProcessor
    ) {
        $this->clientFactory = $clientFactory;
        $this->initTransactionProcessor = $initTransactionProcessor;
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
            $response = $this->initTransactionProcessor->processContinue($transaction, $salesChannelContext);
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
        return false;
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
