<?php
declare(strict_types=1);

namespace Tests\Fixtures\PaywayList;

abstract class PaywayList
{
    public static function getPaywayListResponse(): string
    {
        return file_get_contents(__DIR__ . '/PaywayListResponse.xml');
    }
}
