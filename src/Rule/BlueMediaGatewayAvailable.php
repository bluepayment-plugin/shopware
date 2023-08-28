<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Rule;

use BlueMedia\ShopwarePayment\Struct\GatewayListCollection;
use BlueMedia\ShopwarePayment\Struct\GatewayListStruct;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Symfony\Component\Validator\Constraints\Type;

class BlueMediaGatewayAvailable extends Rule
{
    protected ?string $paymentHandler;

    public function __construct()
    {
        $this->paymentHandler = null;

        parent::__construct();
    }

    public function getName(): string
    {
        return 'blue_media_rule';
    }

    public function match(RuleScope $scope): bool
    {
        if ($scope instanceof CartRuleScope && is_string($this->paymentHandler)) {
            $gatewayListCollection = $scope->getSalesChannelContext()->getExtension(Constants::GATEWAYS_EXTENSION_NAME);

            if ($gatewayListCollection instanceof GatewayListCollection) {
                $gatewayListStruct = $this->getGatewayListForHandler($gatewayListCollection, $this->paymentHandler);

                return $gatewayListStruct instanceof GatewayListStruct && !$gatewayListStruct->isEmpty();
            }
        }

        return false;
    }

    public function getConstraints(): array
    {
        return [
            'paymentHandler' => [ new Type('string') ],
        ];
    }

    private function getGatewayListForHandler(
        GatewayListCollection $gatewayListCollection,
        string $expectedHandler
    ): ?GatewayListStruct {
        return $gatewayListCollection->filter(
            static fn(GatewayListStruct $gatewayListStruct
            ) => $gatewayListStruct->getHandlerIdentifier() === $expectedHandler
        )->first();
    }
}
