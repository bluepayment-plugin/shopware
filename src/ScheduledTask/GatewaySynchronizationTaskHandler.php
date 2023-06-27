<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\ScheduledTask;

use BlueMedia\ShopwarePayment\Service\GatewaySyncService;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class GatewaySynchronizationTaskHandler extends ScheduledTaskHandler
{
    private GatewaySyncService $gatewayService;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        GatewaySyncService $gatewayService
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->gatewayService = $gatewayService;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [ GatewaySynchronizationTask::class ];
    }

    public function run(): void
    {
        $context = new Context(new SystemSource());
        $this->gatewayService->syncGateways($context);
    }
}
