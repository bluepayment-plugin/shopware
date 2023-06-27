<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Exception\NoActiveGatewayIdSelected;
use BlueMedia\ShopwarePayment\PaymentHandler\ApplePayPaymentHandler;
use BlueMedia\ShopwarePayment\PaymentHandler\DetailedPaymentHandler;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Util\GatewayIds;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ApplePayTwigExtension extends AbstractExtension
{
    private SalesChannelContextDataProvider $contextDataProvider;

    public function __construct(
        SalesChannelContextDataProvider $contextDataProvider
    ) {
        $this->contextDataProvider = $contextDataProvider;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isBlueMediaApplePayPayment', [$this, 'isBlueMediaApplePayPayment']),
            new TwigFunction('isBlueMediaApplePayGateway', [$this, 'isBlueMediaApplePayGateway']),
            new TwigFunction('isBlueMediaApplePayGatewaySelected', [$this, 'isBlueMediaApplePayGatewaySelected']),
        ];
    }

    public function isBlueMediaApplePayPayment(PaymentMethodEntity $paymentMethod): bool
    {
        return ApplePayPaymentHandler::class === $paymentMethod->getHandlerIdentifier();
    }

    public function isBlueMediaApplePayGateway(PaymentMethodEntity $paymentMethod, GatewayEntity $gatewayEntity): bool
    {
        return DetailedPaymentHandler::class === $paymentMethod->getHandlerIdentifier()
            && GatewayIds::APPLE_PAY === $gatewayEntity->getGatewayId();
    }

    public function isBlueMediaApplePayGatewaySelected(SalesChannelContext $context): bool
    {
        try {
            $gatewayEntity = $this->contextDataProvider->getSelectedGateway($context);
        } catch (NoActiveGatewayIdSelected $e) {
            return false;
        }

        return $this->isBlueMediaApplePayGateway($context->getPaymentMethod(), $gatewayEntity);
    }
}
