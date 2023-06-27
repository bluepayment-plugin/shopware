<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Currency;

use BlueMedia\ShopwarePayment\Lifecycle\Common\SerializableTrait;
use JsonSerializable;

abstract class AbstractCurrency implements JsonSerializable
{
    use SerializableTrait;

    private const DEFAULT_ROUNDING = ['decimals' => 2, 'interval' => 0.01, 'roundForNet' => true];

    protected string $isoCode;

    protected int $factor = 1;

    protected string $symbol;

    protected int $position = 1;

    protected array $itemRounding = self::DEFAULT_ROUNDING;

    protected array $totalRounding = self::DEFAULT_ROUNDING;

    protected array $translations;

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }
}
