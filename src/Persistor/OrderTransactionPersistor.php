<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Persistor;

use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class OrderTransactionPersistor
{
    private EntityRepositoryInterface $repository;

    public function __construct(
        EntityRepositoryInterface $orderTransactionRepository
    ) {
        $this->repository = $orderTransactionRepository;
    }

    public function updateInitTransactionResponse(string $orderTransactionId, array $data, Context $context): array
    {
        return $this->updateCustomField(
            $orderTransactionId,
            $data,
            Constants::INIT_TRANSACTION_RESPONSE_CUSTOM_FIELD,
            $context
        );
    }

    public function updateBackgroundTransactionResponse(
        string $orderTransactionId,
        array $data,
        Context $context
    ): array {
        return $this->updateCustomField(
            $orderTransactionId,
            $data,
            Constants::BACKGROUND_TRANSACTION_RESPONSE_CUSTOM_FIELD,
            $context
        );
    }

    private function updateCustomField(
        string $orderTransactionId,
        array $data,
        string $customField,
        Context $context
    ): array {
        return $this->repository
            ->update(
                [
                    [
                        'id' => $orderTransactionId,
                        'customFields' => [
                            $customField => $data,
                        ],
                    ],
                ],
                $context
            )->getPrimaryKeys(OrderTransactionDefinition::ENTITY_NAME);
    }
}
