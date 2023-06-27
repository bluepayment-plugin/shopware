<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\BackgroundTransactionFailedException;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Exception\TransactionAlreadyInitializedException;
use BlueMedia\ShopwarePayment\Persistor\OrderTransactionPersistor;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use BlueMedia\ShopwarePayment\Transformer\TransactionTransformer;
use BlueMedia\Transaction\ValueObject\TransactionBackground;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class BackgroundTransactionProcessor
{
    private ClientFactory $clientFactory;

    private TransactionTransformer $transactionTransformer;

    private TransactionDataProvider $transactionDataProvider;

    private OrderTransactionPersistor $transactionPersistor;

    public function __construct(
        ClientFactory $clientFactory,
        TransactionTransformer $transactionTransformer,
        TransactionDataProvider $transactionDataProvider,
        OrderTransactionPersistor $transactionPersistor
    ) {
        $this->clientFactory = $clientFactory;
        $this->transactionTransformer = $transactionTransformer;
        $this->transactionDataProvider = $transactionDataProvider;
        $this->transactionPersistor = $transactionPersistor;
    }

    /**
     * @throws IntegrationNotEnabledException
     * @throws BackgroundTransactionFailedException
     * @throws TransactionAlreadyInitializedException
     * @throws ClientException
     */
    public function process(
        SyncPaymentTransactionStruct $transaction,
        SalesChannelContext $context,
        GatewayEntity $gateway
    ): void {
        $orderTransactionId = $transaction->getOrderTransaction()->getId();
        if (
            $this->transactionDataProvider->hasBackgroundTransactionResponse(
                $orderTransactionId,
                $context->getContext()
            )
        ) {
            throw new TransactionAlreadyInitializedException($orderTransactionId);
        }

        $transactionData = $this->transactionTransformer->transformBackground(
            $transaction,
            [InitTransactionProcessor::PARAM_GATEWAY_ID => $gateway->getGatewayId()]
        );
        $client = $this->clientFactory->createFromPluginConfig($context->getSalesChannelId());

        $result = $client->doTransactionBackground($transactionData)->getData();
        if (false === $result instanceof TransactionBackground) {
            throw new BackgroundTransactionFailedException($orderTransactionId);
        }

        $this->transactionPersistor->updateBackgroundTransactionResponse(
            $transaction->getOrderTransaction()->getId(),
            $result->toArray(),
            $context->getContext()
        );
    }
}
