<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Twig;

use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class QuickTransferTwigExtension extends AbstractExtension
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
            new TwigFunction('shouldDisplayBlueMediaPaymentDetails', [$this, 'shouldDisplayBlueMediaPaymentDetails']),
        ];
    }

    public function shouldDisplayBlueMediaPaymentDetails(
        SalesChannelContext $context,
        ?OrderTransactionEntity $transactionEntity = null
    ): bool {
        if (null === $transactionEntity || false === $this->configProvider->isEnabled($context->getSalesChannelId())) {
            return false;
        }
        $customFields = $transactionEntity->getCustomFields();
        return is_array($customFields)
            && key_exists(Constants::BACKGROUND_TRANSACTION_RESPONSE_CUSTOM_FIELD, $customFields);
    }
}
