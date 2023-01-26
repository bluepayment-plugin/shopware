<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Processor;

use BlueMedia\ShopwarePayment\Exception\InvalidOrderException;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

use function array_filter;
use function sprintf;

class FinalizeTransactionProcessor
{
    private TransactionDataProvider $transactionDataProvider;

    private PaymentHandlerResolver $paymentHandlerResolver;

    private LoggerInterface $logger;

    public function __construct(
        TransactionDataProvider $transactionDataProvider,
        PaymentHandlerResolver $paymentHandlerResolver,
        LoggerInterface $blueMediaFinalizeTransactionLogger
    ) {
        $this->transactionDataProvider = $transactionDataProvider;
        $this->paymentHandlerResolver = $paymentHandlerResolver;
        $this->logger = $blueMediaFinalizeTransactionLogger;
    }

    /**
     * @return array checkout finish page parameters
     * @throws InvalidOrderException
     */
    public function process(Request $request, SalesChannelContext $context): array
    {
        $orderNumber = $request->query->get('OrderID');
        if (empty($orderNumber)) {
            throw new InvalidOrderException();
        }

        $transaction = $this->transactionDataProvider->getTransactionByOrderNumber(
            $orderNumber,
            $context->getContext()
        );
        if (null === $transaction || null === $transaction->getPaymentMethod()) {
            throw new InvalidOrderException($orderNumber);
        }

        $valid = false;

        try {
            $handler = $this->paymentHandlerResolver->resolve($transaction->getPaymentMethod());
            if (null !== $handler) {
                $handler->finalize(
                    new AsyncPaymentTransactionStruct($transaction, $transaction->getOrder(), ''),
                    $request,
                    $context
                );

                $valid = true;
            }
        } catch (Throwable $e) {
            $this->logger->error(sprintf('ERROR on FinalizeTransactionProcessor::process: %s', $e->getMessage()), [
                'request' => $request->query->all(),
                'orderNumber' => $orderNumber,
                'swOrderId' => $transaction->getOrderId(),
                'swTransactionId' => $transaction->getId(),
            ]);
        }

        return array_filter([
            'orderId' => $transaction->getOrderId(),
            'paymentFailed' => false === $valid,
        ]);
    }
}
