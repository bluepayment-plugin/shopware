<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Subscriber;

use BlueMedia\ShopwarePayment\Validator\Constraints\ApplePaySupported;
use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplePayValidationEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'framework.validation.order.create' => 'addApplePayValidation',
        ];
    }

    public function addApplePayValidation(BuildValidationEvent $event): void
    {
        if ('order.create' !== $event->getDefinition()->getName()) {
            return;
        }

        $event->getDefinition()->add(ApplePaySupported::REQUEST_PARAM, new ApplePaySupported());
    }
}
