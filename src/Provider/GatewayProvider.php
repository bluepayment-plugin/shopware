<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Provider;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;

class GatewayProvider
{
    private EntityRepositoryInterface $gatewayRepository;

    public function __construct(
        EntityRepositoryInterface $blueMediaGatewayRepository
    ) {
        $this->gatewayRepository = $blueMediaGatewayRepository;
    }

    public function getIdByExternalId(int $externalId, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('externalId', $externalId));

        return $this->gatewayRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @return string[]
     */
    public function getOrphanedIdsForSalesChannel(array $existingIds, string $salesChannelId, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('gatewaySalesChannels.salesChannelId', $salesChannelId));
        $criteria->addFilter(new NotFilter(
            MultiFilter::CONNECTION_AND,
            [
                new EqualsAnyFilter('id', $existingIds),
            ]
        ));

        return $this->gatewayRepository->searchIds($criteria, $context)->getIds();
    }
}
