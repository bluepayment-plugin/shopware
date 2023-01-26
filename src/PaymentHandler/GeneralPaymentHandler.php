<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\PaymentHandler;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Exception\ConfirmationCheckFailedException;
use BlueMedia\ShopwarePayment\Transformer\TransactionTransformer;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentFinalizeException;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class GeneralPaymentHandler implements BlueMediaPaymentHandlerInterface
{
    private ClientFactory $clientFactory;

    private TransactionTransformer $transactionTransformer;

    public function __construct(
        ClientFactory $clientFactory,
        TransactionTransformer $transactionTransformer
    ) {
        $this->clientFactory = $clientFactory;
        $this->transactionTransformer = $transactionTransformer;
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
            $client = $this->clientFactory->createFromPluginConfig($salesChannelContext->getSalesChannelId());

            $transactionData = $this->transactionTransformer->transform($transaction);

            $redirectUrl = $client->getTransactionRedirectUrl($transactionData);
        } catch (Throwable $e) {
            throw new AsyncPaymentProcessException(
                $transaction->getOrderTransaction()->getId(),
                $e->getMessage()
            );
        }

        return new RedirectResponse($redirectUrl);
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
}
