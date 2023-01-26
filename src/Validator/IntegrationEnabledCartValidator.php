<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator;

use BlueMedia\ShopwarePayment\PaymentHandler\GeneralPaymentHandler;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Payment\Cart\Error\PaymentMethodBlockedError;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class IntegrationEnabledCartValidator implements CartValidatorInterface
{
    private ConfigProvider $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
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
        return $paymentMethod->getHandlerIdentifier() === GeneralPaymentHandler::class;
    }
}
