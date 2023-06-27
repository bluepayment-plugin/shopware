<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GatewayListTwigExtension extends AbstractExtension
{
    private PaymentHandlerResolver $handlerResolver;

    public function __construct(
        PaymentHandlerResolver $handlerResolver
    ) {
        $this->handlerResolver = $handlerResolver;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('shouldDisplayBlueMediaGatewayList', [$this, 'shouldDisplayBlueMediaGatewayList']),
        ];
    }

    public function shouldDisplayBlueMediaGatewayList(
        PaymentMethodEntity $paymentMethod,
        SalesChannelContext $context
    ): bool {
        return $context->getPaymentMethod()->getId() === $paymentMethod->getId()
            && in_array(
                $context->getPaymentMethod()->getHandlerIdentifier(),
                $this->handlerResolver->getGatewayRequiredHandlersNames(),
                true
            );
    }
}
