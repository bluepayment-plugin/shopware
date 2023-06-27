<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator;

use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Payment\Cart\Error\PaymentMethodBlockedError;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class IntegrationEnabledCartValidator implements CartValidatorInterface
{
    private ConfigProvider $configProvider;

    private PaymentHandlerResolver $handlerResolver;

    public function __construct(
        ConfigProvider $configProvider,
        PaymentHandlerResolver $handlerResolver
    ) {
        $this->configProvider = $configProvider;
        $this->handlerResolver = $handlerResolver;
    }

    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        if (
            $this->isBlueMediaPayment($context->getPaymentMethod()) &&
            false === $this->configProvider->isEnabled($context->getSalesChannelId())
        ) {
            $errors->add(
                new PaymentMethodBlockedError((string) $context->getPaymentMethod()->getTranslation('name'))
            );
        }
    }

    private function isBlueMediaPayment(PaymentMethodEntity $paymentMethod): bool
    {
        return in_array($paymentMethod->getHandlerIdentifier(), $this->getBlueMediaPaymentHandlersList(), true);
    }

    private function getBlueMediaPaymentHandlersList(): array
    {
        $handlers = [];
        foreach ($this->handlerResolver->getHandlers() as $handler) {
            $handlers[] = get_class($handler);
        }

        return $handlers;
    }
}
