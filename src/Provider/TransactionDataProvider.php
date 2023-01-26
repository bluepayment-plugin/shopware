<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class TransactionDataProvider
{
    private EntityRepositoryInterface $orderTransactionRepository;

    public function __construct(
        EntityRepositoryInterface $orderTransactionRepository
    ) {
        $this->orderTransactionRepository = $orderTransactionRepository;
    }

    public function getTransactionByOrderNumber(string $orderNumber, Context $context): ?OrderTransactionEntity
    {
        $criteria = $this->getCriteria();
        $criteria->addFilter(new EqualsFilter('order.orderNumber', $orderNumber));
        return $this->returnFirstByCriteria($criteria, $context);
    }

    public function getTransactionById(string $orderTransactionId, Context $context): ?OrderTransactionEntity
    {
        $criteria = $this->getCriteria();
        $criteria->addFilter(new EqualsFilter('id', $orderTransactionId));
        return $this->returnFirstByCriteria($criteria, $context);
    }

    private function getCriteria(): Criteria
    {
        $criteria = new Criteria();
        $criteria->addAssociation('order');
        $criteria->addAssociation('paymentMethod');
        $criteria->addFilter(new EqualsFilter(
            'paymentMethod.handlerIdentifier',
            GeneralPaymentHandler::class
        ));
        return $criteria;
    }

    private function returnFirstByCriteria(Criteria $criteria, Context $context): ?OrderTransactionEntity
    {
        try {
            return $this->orderTransactionRepository->search($criteria, $context)->first();
        } catch (InconsistentCriteriaIdsException $e) {
            return null;
        }
    }
}
