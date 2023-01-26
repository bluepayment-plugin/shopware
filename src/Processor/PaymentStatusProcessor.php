<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\ShopwarePayment\Api\Client;
use BlueMedia\ShopwarePayment\Api\ClientFactory;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Exception\IntegrationNotEnabledException;
use BlueMedia\ShopwarePayment\Exception\InvalidAmountException;
use BlueMedia\ShopwarePayment\Exception\InvalidRequestParamsException;
use BlueMedia\ShopwarePayment\Exception\InvalidTransactionException;
use BlueMedia\ShopwarePayment\Exception\NoOrderTransactionException;
use BlueMedia\ShopwarePayment\Validator\TransactionValidator;
use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class PaymentStatusProcessor
{
    private const ITN_TRANSACTIONS_PARAM = 'transactions';

    private ClientFactory $clientFactory;

    private Client $dummyClient;

    private TransactionValidator $transactionValidator;

    private OrderTransactionStateHandler $transactionStateHandler;

    private LoggerInterface $logger;

    public function __construct(
        ClientFactory $clientFactory,
        TransactionValidator $transactionValidator,
        OrderTransactionStateHandler $transactionStateHandler,
        LoggerInterface $blueMediaWebhookLogger
    ) {
        $this->clientFactory = $clientFactory;
        $this->dummyClient = $clientFactory->createDummyClient();
        $this->transactionValidator = $transactionValidator;
        $this->logger = $blueMediaWebhookLogger;
        $this->transactionStateHandler = $transactionStateHandler;
    }

    /**
     * @throws InvalidRequestParamsException
     * @throws InvalidTransactionException
     * @throws IntegrationNotEnabledException
     * @throws ClientException
     * @throws NoOrderTransactionException
     */
    public function process(Request $request, SalesChannelContext $salesChannelContext): string
    {
        $context = $salesChannelContext->getContext();
        /** @var Itn $itnIn expecting ITN data */
        $itnIn = $this->getItnInResponse($request)->getData();

        $orderTransactionEntity = $this->transactionValidator->getValidatedOrderTransaction($itnIn, $context);
        $client = $this->getTransactionClient($orderTransactionEntity);

        $transactionConfirmed = false;
        //process transaction only when hash is correct
        if ($client->checkHash($itnIn)) {
            $transactionConfirmed = $this->processTransaction($orderTransactionEntity, $itnIn, $context);
        }

        return $client->doItnInResponse($itnIn, $transactionConfirmed)->getData()->toXml();
    }

    public function logException(Exception $exception): void
    {
        $this->logger->error(
            sprintf('ERROR on PaymentStatusProcessor::process: %s', $exception->getMessage()),
            [
                'error'   => get_class($exception),
                'trace'   => $exception->getTraceAsString(),
            ]
        );
    }

    private function processTransaction(OrderTransactionEntity $transaction, Itn $itnIn, Context $context): bool
    {
        try {
            $this->transactionValidator->validateTransactionData($transaction, $itnIn, $context);
            $isPayed = $this->transactionValidator->isTransactionPaid($itnIn);
            //skip status change on transactions in progress
            if ($isPayed !== null) {
                $this->changeTransactionState($transaction, $itnIn, $isPayed, $context);
            }
        } catch (
            InvalidAmountException
            | NoOrderTransactionException
            | InvalidRequestParamsException $exception
        ) {
            $this->logException($exception);
            return false;
        }

        return true;
    }

    /**
     * @throws InvalidRequestParamsException
     */
    private function getItnInResponse(Request $request): Response
    {
        $transactions = $request->get(self::ITN_TRANSACTIONS_PARAM);
        if ($transactions === null) {
            throw new InvalidRequestParamsException('Missing transactions parameter on ITN request.');
        }

        try {
            return $this->dummyClient->doItnIn($transactions);
        } catch (ClientException $exception) {
            throw new InvalidRequestParamsException($exception->getMessage());
        }
    }

    /**
     * @throws IntegrationNotEnabledException
     * @throws NoOrderTransactionException
     */
    private function getTransactionClient(OrderTransactionEntity $orderTransactionEntity): Client
    {
        if (!$orderTransactionEntity->getOrder()) {
            throw new NoOrderTransactionException();
        }
        $orderSalesChannelId = $orderTransactionEntity->getOrder()->getSalesChannelId();

        return $this->clientFactory->createFromPluginConfig($orderSalesChannelId);
    }

    private function changeTransactionState(
        OrderTransactionEntity $transaction,
        Itn $itnIn,
        bool $isPayed,
        Context $context
    ): void {
        $transactionState = $transaction->getStateMachineState()->getTechnicalName();

        if (
            !in_array($transactionState, [OrderTransactionStates::STATE_CANCELLED, OrderTransactionStates::STATE_OPEN])
        ) {
            return;
        }

        if ($isPayed) {
            if ($transactionState === OrderTransactionStates::STATE_CANCELLED) {
                $this->transactionStateHandler->reopen($transaction->getId(), $context);
            }
            $this->transactionStateHandler->paid($transaction->getId(), $context);
            $this->logger->info(
                sprintf(
                    'Blue Media Order (%s) Transaction (%s) Paid with remoteID (%s).',
                    $itnIn->getOrderID(),
                    $transaction->getId(),
                    $itnIn->getRemoteID()
                )
            );
        } elseif ($transactionState === OrderTransactionStates::STATE_OPEN) {
            $this->transactionStateHandler->cancel($transaction->getId(), $context);
        }
    }
}
