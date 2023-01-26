<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Rules;

use BlueMedia\ShopwarePayment\Lifecycle\Common\SerializableTrait;
use JsonSerializable;

abstract class AbstractRule implements JsonSerializable
{
    use SerializableTrait;

    protected string $id;

    protected string $name;

    protected string $description;

    protected int $priority;

    protected array $moduleTypes = [];

    protected array $conditions = [];
}
