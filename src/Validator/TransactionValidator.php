<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator;

use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\ShopwarePayment\Exception\InvalidAmountException;
use BlueMedia\ShopwarePayment\Exception\InvalidRequestParamsException;
use BlueMedia\ShopwarePayment\Exception\InvalidTransactionException;
use BlueMedia\ShopwarePayment\Exception\NoOrderTransactionException;
use BlueMedia\ShopwarePayment\Provider\TransactionDataProvider;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class TransactionValidator
{
    private const PAYMENT_STATUS_SUCCESS = 'SUCCESS';

    private const PAYMENT_STATUS_PENDING = 'PENDING';

    private const PAYMENT_STATUS_FAILURE = 'FAILURE';

    private EntityRepositoryInterface $currencyRepository;

    private TransactionDataProvider $transactionDataProvider;

    public function __construct(
        EntityRepositoryInterface $currencyRepository,
        TransactionDataProvider $transactionDataProvider
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->transactionDataProvider = $transactionDataProvider;
    }

    /**
     * @throws InvalidTransactionException
     */
    public function getValidatedOrderTransaction(Itn $itnIn, Context $context): OrderTransactionEntity
    {
        $orderTransactionEntity = $this->transactionDataProvider->getTransactionByOrderNumber(
            $itnIn->getOrderID(),
            $context
        );
        if (!$orderTransactionEntity instanceof OrderTransactionEntity) {
            throw new InvalidTransactionException($itnIn->getOrderID());
        }
        return $orderTransactionEntity;
    }

    /**
     * @throws InvalidRequestParamsException
     */
    public function isTransactionPaid(Itn $itnIn): ?bool
    {
        switch ($itnIn->getPaymentStatus()) {
            case self::PAYMENT_STATUS_PENDING:
                return null;
            case self::PAYMENT_STATUS_SUCCESS:
                return true;
            case self::PAYMENT_STATUS_FAILURE:
                return false;
            default:
                throw new InvalidRequestParamsException(
                    sprintf('Invalid Order transaction payment status: %s.', $itnIn->getPaymentStatus())
                );
        }
    }

    /**
     * @throws InvalidAmountException
     * @throws NoOrderTransactionException
     */
    public function validateTransactionData(OrderTransactionEntity $transaction, Itn $itnIn, Context $context): void
    {
        $order = $transaction->getOrder();
        if (null === $order) {
            throw new NoOrderTransactionException();
        }

        if (
            false === $this->isValidAmount($itnIn->getAmount(), $order->getAmountTotal())
            || false === $this->isValidCurrency($itnIn->getCurrency(), $order->getCurrencyId(), $context)
        ) {
            throw new InvalidAmountException();
        }
    }

    private function isValidAmount(string $requestAmount, float $orderAmount): bool
    {
        return round((float)$requestAmount, 2) === round($orderAmount, 2);
    }

    private function isValidCurrency(string $currencyCode, string $currencyId, Context $context): bool
    {
        try {
            $currency = $this->currencyRepository->search(new Criteria([$currencyId]), $context)->first();
        } catch (InconsistentCriteriaIdsException $e) {
            return false;
        }

        return $currency !== null && $currency->getIsoCode() === $currencyCode;
    }
}
