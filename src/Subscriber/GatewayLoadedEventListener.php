<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Subscriber;

use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayDefinition;
use BlueMedia\ShopwarePayment\Entity\Gateway\GatewayEntity;
use BlueMedia\ShopwarePayment\Resolver\PaymentHandlerResolver;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GatewayLoadedEventListener implements EventSubscriberInterface
{
    private PaymentHandlerResolver $paymentHandlerResolver;

    public function __construct(
        PaymentHandlerResolver $paymentHandlerResolver
    ) {
        $this->paymentHandlerResolver = $paymentHandlerResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GatewayDefinition::ENTITY_NAME . '.loaded' => 'onLoaded',
        ];
    }

    public function onLoaded(EntityLoadedEvent $event): void
    {
        foreach ($event->getEntities() as $gatewayEntity) {
            if ($gatewayEntity instanceof GatewayEntity) {
                 $gatewayEntity->setIsSupported($this->paymentHandlerResolver->isGatewaySupported($gatewayEntity));
            }
        }
    }
}
