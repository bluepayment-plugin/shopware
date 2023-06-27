<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Exception\TransactionAlreadyInitializedException;
use BlueMedia\ShopwarePayment\Exception\TransactionInitFailedException;
use BlueMedia\ShopwarePayment\Persistor\OrderTransactionPersistor;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use BlueMedia\ShopwarePayment\Transformer\TransactionTransformer;
use BlueMedia\Transaction\ValueObject\TransactionContinue;
use BlueMedia\Transaction\ValueObject\TransactionInit;
use Shopware\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class InitTransactionProcessor
{
    public const PARAM_SCREEN_TYPE = 'screenType';

    public const PARAM_GATEWAY_ID = 'gatewayID';

    public const PARAM_PAYMENT_TOKEN = 'paymentToken';

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
     * @throws TransactionInitFailedException
     * @throws TransactionAlreadyInitializedException
     * @throws ClientException
     */
    public function process(
        SyncPaymentTransactionStruct $transaction,
        SalesChannelContext $context,
        array $params = []
    ): TransactionContinue {
        $salesChannelId = $context->getSalesChannelId();
        $client = $this->clientFactory->createFromPluginConfig($salesChannelId);

        $orderTransactionId = $transaction->getOrderTransaction()->getId();
        if ($this->transactionDataProvider->hasInitTransactionResponse($orderTransactionId, $context->getContext())) {
            throw new TransactionAlreadyInitializedException($orderTransactionId);
        }

        $transactionData = $this->transactionTransformer->transform($transaction, $params);

        $result = $client->doTransactionInit($transactionData)->getData();
        if (false === $result instanceof TransactionContinue) {
            throw new TransactionInitFailedException(
                $orderTransactionId,
                $result instanceof TransactionInit ? $result->getReason() : null
            );
        }

        $this->transactionPersistor->updateInitTransactionResponse(
            $transaction->getOrderTransaction()->getId(),
            $result->toArray(),
            $context->getContext()
        );

        return $result;
    }
}
