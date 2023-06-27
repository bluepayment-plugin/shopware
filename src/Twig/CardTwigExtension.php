<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\PaymentHandler\CardPaymentHandler;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Struct\GatewayListCollection;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CardTwigExtension extends AbstractExtension
{
    private ConfigProvider $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {

        $this->configProvider = $configProvider;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('blueMediaCardPaymentAvailable', [$this, 'blueMediaCardPaymentAvailable']),
            new TwigFunction('checkoutWithBlueMediaCardPayment', [$this, 'checkoutWithBlueMediaCardPayment']),
        ];
    }

    public function blueMediaCardPaymentAvailable(
        SalesChannelContext $context
    ): bool {
        if (false === $this->configProvider->isEnabled($context->getSalesChannelId())) {
            return false;
        }
        $gatewayListCollection = $context->getExtension(Constants::GATEWAYS_EXTENSION_NAME);
        if ($gatewayListCollection instanceof GatewayListCollection) {
            foreach ($gatewayListCollection as $gatewayListStruct) {
                if (
                    $gatewayListStruct->getHandlerIdentifier() === CardPaymentHandler::class
                    && !$gatewayListStruct->isEmpty()
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    public function checkoutWithBlueMediaCardPayment(SalesChannelContext $context): bool
    {
        return $context->getPaymentMethod()->getHandlerIdentifier() === CardPaymentHandler::class;
    }
}
