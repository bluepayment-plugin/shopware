<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Context;

use RuntimeException;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlueMediaContextFactory
{
    private EntityRepositoryInterface $salesChannelRepository;

    /**
     * @internal
     */
    public function __construct(
        EntityRepositoryInterface $salesChannelRepository
    ) {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    public function create(string $salesChannelId, ?Context $context = null): BlueMediaContext
    {
        if (null === $context) {
            $context = new Context(new SystemSource());
        }

        $criteria = new Criteria([$salesChannelId]);
        $criteria->addAssociation('currency');
        $criteria->addAssociation('domains');
        $criteria->addAssociation('language');
        $criteria->addAssociation('languages');

        /** @var SalesChannelEntity|null $salesChannel */
        $salesChannel = $this->salesChannelRepository->search($criteria, $context)
            ->get($salesChannelId);

        if (!$salesChannel) {
            throw new RuntimeException(sprintf('Sales channel with id %s not found or not valid!', $salesChannelId));
        }

        return new BlueMediaContext(
            new Context(new SystemSource(), [], $salesChannel->getCurrencyId(), [$salesChannel->getLanguageId()]),
            $salesChannel
        );
    }
}
