<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use BlueMedia\Serializer\Serializer;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use BlueMedia\ShopwarePayment\Util\Constants;
use BlueMedia\Transaction\ValueObject\TransactionBackground;
use BlueMedia\Transaction\ValueObject\TransactionContinue;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;

class TransactionDataProvider
{
    private EntityRepositoryInterface $orderTransactionRepository;

    private Serializer $serializer;

    private PaymentHandlerResolver $handlerResolver;

    public function __construct(
        EntityRepositoryInterface $orderTransactionRepository,
        PaymentHandlerResolver $handlerResolver
    ) {
        $this->orderTransactionRepository = $orderTransactionRepository;

        $this->serializer = new Serializer();
        $this->handlerResolver = $handlerResolver;
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

    public function getTransactionByOrderId(string $orderId, Context $context): ?OrderTransactionEntity
    {
        $criteria = $this->getCriteria();
        $criteria->addFilter(new EqualsFilter('orderId', $orderId));

        return $this->returnFirstByCriteria($criteria, $context);
    }

    public function getInitTransactionResponse(string $orderTransactionId, Context $context): ?TransactionContinue
    {
        $response = $this->getTransactionCustomField(
            $orderTransactionId,
            Constants::INIT_TRANSACTION_RESPONSE_CUSTOM_FIELD,
            $context
        );
        if (null === $response) {
            return null;
        }

        return $this->serializer->fromArray($response, TransactionContinue::class);
    }

    public function getBackgroundTransactionResponse(
        string $orderTransactionId,
        Context $context
    ): ?TransactionBackground {
        $response = $this->getTransactionCustomField(
            $orderTransactionId,
            Constants::BACKGROUND_TRANSACTION_RESPONSE_CUSTOM_FIELD,
            $context
        );
        if (null === $response) {
            return null;
        }

        return $this->serializer->fromArray($response, TransactionBackground::class);
    }

    public function hasInitTransactionResponse(string $orderTransactionId, Context $context): bool
    {
        return false === empty($this->getInitTransactionResponse($orderTransactionId, $context));
    }

    public function hasBackgroundTransactionResponse(string $orderTransactionId, Context $context): bool
    {
        return false === empty($this->getBackgroundTransactionResponse($orderTransactionId, $context));
    }

    private function getTransactionCustomField(
        string $orderTransactionId,
        string $customField,
        Context $context
    ): ?array {
        $entity = $this->getTransactionById($orderTransactionId, $context);
        if (null === $entity) {
            return null;
        }

        return $entity->getCustomFields()[$customField] ?? null;
    }

    private function getCriteria(): Criteria
    {
        $criteria = new Criteria();
        $criteria->addAssociation('order');
        $criteria->addAssociation('paymentMethod');

        $queries = [];
        foreach ($this->handlerResolver->getHandlers() as $handler) {
            $queries[] = new EqualsFilter('paymentMethod.handlerIdentifier', get_class($handler));
        }
        $criteria->addFilter(
            new OrFilter($queries)
        );

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
