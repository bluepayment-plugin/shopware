<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayCollection;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class GatewayProvider
{
    private EntityRepositoryInterface $gatewayRepository;

    public function __construct(
        EntityRepositoryInterface $blueMediaGatewayRepository
    ) {
        $this->gatewayRepository = $blueMediaGatewayRepository;
    }

    public function getActiveGatewayById(
        string $id,
        SalesChannelContext $context,
        ?float $cartAmount = null
    ): ?GatewayEntity {
        $criteria = $this->getActiveAndEnabledCriteria($context, $cartAmount);
        $criteria->addFilter(new EqualsFilter('id', $id));

        /** @var GatewayCollection $result */
        $result = $this->gatewayRepository->search($criteria, $context->getContext())->getEntities();
        $result->reduceToSupported();

        return $result->first();
    }

    public function getActiveGateways(SalesChannelContext $context, float $cartAmount): GatewayCollection
    {
        $criteria = $this->getActiveAndEnabledCriteria($context, $cartAmount);

        /** @var GatewayCollection $result */
        $result = $this->gatewayRepository->search($criteria, $context->getContext())->getEntities();
        $result->reduceToSupported();

        return $result;
    }

    public function getIdByGatewayId(int $gatewayId, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('gatewayId', $gatewayId));

        return $this->gatewayRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @return string[]
     */
    public function getOrphanedIdsForSalesChannel(array $existingIds, string $salesChannelId, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelsActive.id', $salesChannelId));
        $criteria->addFilter(new NotFilter(
            MultiFilter::CONNECTION_AND,
            [
                new EqualsAnyFilter('id', $existingIds),
            ]
        ));

        return $this->gatewayRepository->searchIds($criteria, $context)->getIds();
    }

    private function getActiveAndEnabledCriteria(SalesChannelContext $context, ?float $cartAmount = null): Criteria
    {
        $criteria = new Criteria();
        $criteria->addAssociation('logoMedia');
        $criteria->addAssociation('currencies.currency');

        $criteria->addFilter(new EqualsFilter('salesChannelsActive.id', $context->getSalesChannelId()));
        $criteria->addFilter(new EqualsFilter('salesChannelsEnabled.id', $context->getSalesChannelId()));
        $criteria->addFilter(new EqualsFilter('currencies.currencyId', $context->getCurrency()->getId()));

        if (null === $cartAmount) {
            $criteria->addFilter(new OrFilter([
                new AndFilter([
                    new RangeFilter('currencies.minCartAmount', [RangeFilter::LTE => $cartAmount]),
                    new RangeFilter('currencies.maxCartAmount', [RangeFilter::GTE => $cartAmount]),
                ]),
                new AndFilter([
                    new EqualsFilter('currencies.minCartAmount', null),
                    new EqualsFilter('currencies.maxCartAmount', null),
                ]),
                new AndFilter([
                    new RangeFilter('currencies.minCartAmount', [RangeFilter::LTE => $cartAmount]),
                    new EqualsFilter('currencies.maxCartAmount', null),
                ]),
                new AndFilter([
                    new EqualsFilter('currencies.minCartAmount', null),
                    new RangeFilter('currencies.maxCartAmount', [RangeFilter::GTE => $cartAmount]),
                ]),
            ]));
        }

        return $criteria;
    }
}
