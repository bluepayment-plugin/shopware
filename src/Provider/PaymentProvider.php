<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class PaymentProvider
{
    private EntityRepositoryInterface $paymentMethodRepository;

    public function __construct(EntityRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getPaymentMethodIdByHandler(string $handlerClass, Context $context): ?string
    {
        try {
            $paymentCriteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', $handlerClass));
            $paymentIds = $this->paymentMethodRepository->searchIds($paymentCriteria, $context);
        } catch (InconsistentCriteriaIdsException $e) {
            return null;
        }

        if (0 === $paymentIds->getTotal()) {
            return null;
        }

        return $paymentIds->firstId();
    }

    public function getPaymentMethodByHandler(string $handlerClass, Context $context): ?PaymentMethodEntity
    {
        try {
            $paymentCriteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', $handlerClass));
            $paymentSearchResults = $this->paymentMethodRepository->search($paymentCriteria, $context);
        } catch (InconsistentCriteriaIdsException $e) {
            return null;
        }

        if (0 === $paymentSearchResults->getTotal()) {
            return null;
        }

        return $paymentSearchResults->getEntities()->first();
    }
}
