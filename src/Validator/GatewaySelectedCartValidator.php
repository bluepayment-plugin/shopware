<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Validator;

use BlueMedia\ShopwarePayment\Exception\NoActiveGatewayIdSelected;
use BlueMedia\ShopwarePayment\PaymentHandler\BlueMediaPaymentHandlerInterface;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use BlueMedia\ShopwarePayment\Validator\Error\GatewayNotSelectedError;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class GatewaySelectedCartValidator implements CartValidatorInterface
{
    private SalesChannelContextDataProvider $contextDataProvider;

    private PaymentHandlerResolver $handlerResolver;

    public function __construct(
        SalesChannelContextDataProvider $contextDataProvider,
        PaymentHandlerResolver $handlerResolver
    ) {
        $this->contextDataProvider = $contextDataProvider;
        $this->handlerResolver = $handlerResolver;
    }

    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        if (
            in_array(
                $context->getPaymentMethod()->getHandlerIdentifier(),
                $this->handlerResolver->getGatewayRequiredHandlersNames(),
                true
            )
            && !$this->isGatewaySelectedAndValid($context, $cart->getPrice()->getTotalPrice())
        ) {
            $errors->add(
                new GatewayNotSelectedError((string)$context->getPaymentMethod()->getTranslation('name'))
            );
        }
    }

    private function isGatewaySelectedAndValid(SalesChannelContext $context, float $cartAmount): bool
    {
        try {
            $selectedGateway = $this->contextDataProvider->getSelectedGateway($context, $cartAmount);
        } catch (NoActiveGatewayIdSelected $e) {
            return false;
        }
        $paymentHandler = $this->handlerResolver->resolve($context->getPaymentMethod());

        return $paymentHandler instanceof BlueMediaPaymentHandlerInterface
            && $paymentHandler->isGatewaySupported($selectedGateway);
    }
}
