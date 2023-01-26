<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class GatewaySynchronizationTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'blue_media.gateway_synchronization';
    }

    public static function getDefaultInterval(): int
    {
        return 60 * 60; // 1h
    }
}
