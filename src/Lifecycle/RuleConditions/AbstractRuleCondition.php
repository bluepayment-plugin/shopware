<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\RuleConditions;

use BlueMedia\ShopwarePayment\Lifecycle\Common\SerializableTrait;
use JsonSerializable;

abstract class AbstractRuleCondition implements JsonSerializable
{
    use SerializableTrait;

    protected string $id;

    protected string $type;

    protected array $value = [];

    protected string $ruleId;

    protected ?string $parentId = null;
}
