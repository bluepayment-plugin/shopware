<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\RuleConditions;

use BlueMedia\ShopwarePayment\Rule\BlueMediaGatewayAvailable;
use Shopware\Core\Framework\Uuid\Uuid;

class GatewayAvailableRuleCondition extends AbstractRuleCondition
{
    public function __construct(
        string $ruleId,
        string $paymentHandler,
        ?string $parentId = null
    ) {
        $this->id = Uuid::randomHex();
        $this->type = (new BlueMediaGatewayAvailable())->getName();
        $this->ruleId = $ruleId;
        $this->parentId = $parentId;
        $this->value = [
            'paymentHandler' => $paymentHandler,
        ];
    }
}
