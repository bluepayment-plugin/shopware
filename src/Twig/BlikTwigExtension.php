<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\PaymentHandler\BlikPaymentHandler;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlikTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isBlueMediaBlikPaymentSelected', [$this, 'isBlueMediaBlikPaymentSelected']),
        ];
    }

    public function isBlueMediaBlikPaymentSelected(
        SalesChannelContext $salesChannelContext
    ): bool {
        return BlikPaymentHandler::class === $salesChannelContext->getPaymentMethod()->getHandlerIdentifier();
    }
}
