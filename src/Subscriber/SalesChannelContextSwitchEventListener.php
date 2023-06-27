<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Subscriber;

use BlueMedia\ShopwarePayment\Persistor\SalesChannelContextPersistor;
use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Provider\SalesChannelContextDataProvider;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextSwitchEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SalesChannelContextSwitchEventListener implements EventSubscriberInterface
{
    private ConfigProvider $configProvider;

    private RequestStack $requestStack;

    private SalesChannelContextPersistor $contextPersistor;

    private SalesChannelContextDataProvider $contextDataProvider;

    public function __construct(
        ConfigProvider $configProvider,
        RequestStack $requestStack,
        SalesChannelContextPersistor $contextPersistor,
        SalesChannelContextDataProvider $contextDataProvider
    ) {
        $this->configProvider = $configProvider;
        $this->requestStack = $requestStack;
        $this->contextPersistor = $contextPersistor;
        $this->contextDataProvider = $contextDataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SalesChannelContextSwitchEvent::class => 'persistSelectedGatewayId',
        ];
    }

    public function persistSelectedGatewayId(SalesChannelContextSwitchEvent $event): void
    {
        if (false === $this->configProvider->isEnabled($event->getSalesChannelContext()->getSalesChannelId())) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $gatewayId = $request->get(Constants::REQUEST_PARAMETER_GATEWAY_ID);
        if (empty($gatewayId) || false === $this->isGatewayChanged($gatewayId, $event->getSalesChannelContext())) {
            return;
        }

        $this->contextPersistor->persistBlueMediaGateway(
            $gatewayId,
            $event->getSalesChannelContext()
        );
    }

    private function isGatewayChanged(?string $newGatewayId, SalesChannelContext $context): bool
    {
        $currentGatewayId = $this->contextDataProvider->getSelectedId($context);

        return $newGatewayId !== $currentGatewayId;
    }
}
