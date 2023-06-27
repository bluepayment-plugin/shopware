<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Cart;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayCollection;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\PaymentHandler\BlueMediaPaymentHandlerInterface;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Provider\GatewayProvider;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use BlueMedia\ShopwarePayment\Service\GatewayGroupingService;
use BlueMedia\ShopwarePayment\Struct\GatewayListCollection;
use BlueMedia\ShopwarePayment\Struct\GatewayListStruct;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class GatewayListDataCollector implements CartDataCollectorInterface
{
    private ConfigProvider $configProvider;

    private GatewayProvider $gatewayProvider;

    private SalesChannelContextDataProvider $contextDataProvider;

    private GatewayGroupingService $gatewayGroupingService;

    private PaymentHandlerResolver $handlerResolver;

    public function __construct(
        ConfigProvider $configProvider,
        GatewayProvider $gatewayProvider,
        PaymentHandlerResolver $handlerResolver,
        SalesChannelContextDataProvider $contextDataProvider,
        GatewayGroupingService $gatewayGroupingService
    ) {
        $this->configProvider = $configProvider;
        $this->gatewayProvider = $gatewayProvider;
        $this->contextDataProvider = $contextDataProvider;
        $this->gatewayGroupingService = $gatewayGroupingService;
        $this->handlerResolver = $handlerResolver;
    }

    public function collect(
        CartDataCollection $data,
        Cart $original,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        $this->attachGatewayList($original, $context);
        $this->attachSelectedGateway($context);
    }

    private function attachGatewayList(Cart $cart, SalesChannelContext $context): void
    {
        if (false === $this->configProvider->isEnabled($context->getSalesChannelId())) {
            return;
        }

        $gatewayListCollection = $this->getGatewayListCollection($context);
        if (0 !== $gatewayListCollection->count()) {
            return;
        }

        $gateways = $this->gatewayProvider->getActiveGateways($context, $cart->getPrice()->getTotalPrice());
        foreach ($this->handlerResolver->getHandlers() as $handler) {
            $gatewayListCollection->add(
                $this->buildGatewayListForHandler($gateways, $handler)
            );
        }

        $context->addExtension(
            Constants::GATEWAYS_EXTENSION_NAME,
            $gatewayListCollection
        );
    }

    private function attachSelectedGateway(SalesChannelContext $context): void
    {
        if (false === $this->configProvider->isEnabled($context->getSalesChannelId())) {
            return;
        }

        $gatewayListCollection = $this->getGatewayListCollection($context);
        $gatewayListStruct = $gatewayListCollection->first();
        if (!$gatewayListStruct instanceof GatewayListStruct || $gatewayListStruct->hasSelectedGatewayId()) {
            return;
        }

        $selectedGatewayId = $this->contextDataProvider->getSelectedId($context);

        foreach ($gatewayListCollection as $gatewayListStruct) {
            $gatewayListStruct->setSelectedGatewayId($selectedGatewayId);
        }
    }

    private function getGatewayListCollection(SalesChannelContext $context): GatewayListCollection
    {
        $extension = $context->getExtension(Constants::GATEWAYS_EXTENSION_NAME);
        if ($extension instanceof GatewayListCollection) {
            return $extension;
        }

        return new GatewayListCollection();
    }

    private function buildGatewayListForHandler(
        GatewayCollection $gateways,
        BlueMediaPaymentHandlerInterface $handler
    ): GatewayListStruct {
        //filter out not supported
        $gateways = $gateways->filter(fn(GatewayEntity $gatewayEntity) => $handler->isGatewaySupported($gatewayEntity));

        $gatewayGroupCollection = null;

        if ($handler->gatewayGroupingSupported()) {
            $gatewayGroupCollection = $this->gatewayGroupingService->groupGateways($gateways);
        }

        return new GatewayListStruct(
            $gatewayGroupCollection,
            ($gatewayGroupCollection === null) ? $gateways : null,
            get_class($handler)
        );
    }
}
