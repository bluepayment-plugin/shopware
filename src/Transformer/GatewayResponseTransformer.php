<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Transformer;

use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\PaywayList\ValueObject\PaywayListResponse\Gateway;
use BlueMedia\PaywayList\ValueObject\PaywayListResponse\PaywayListResponse;
use BlueMedia\ShopwarePayment\Context\BlueMediaContext;
use BlueMedia\ShopwarePayment\Provider\CurrencyProvider;
use BlueMedia\ShopwarePayment\Provider\GatewayCurrencyProvider;
use BlueMedia\ShopwarePayment\Provider\GatewayProvider;
use BlueMedia\ShopwarePayment\Provider\GatewaySalesChannelProvider;
use BlueMedia\ShopwarePayment\Service\MediaDownloader;
use BlueMedia\ShopwarePayment\Util\Constants;
use Shopware\Core\Framework\Uuid\Uuid;

class GatewayResponseTransformer
{
    private GatewayProvider $gatewayProvider;

    private GatewayCurrencyProvider $gatewayCurrencyProvider;

    private GatewaySalesChannelProvider $gatewaySalesChannelProvider;

    private MediaDownloader $mediaDownloader;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        GatewayProvider $gatewayProvider,
        GatewayCurrencyProvider $gatewayCurrencyProvider,
        GatewaySalesChannelProvider $gatewaySalesChannelProvider,
        MediaDownloader $mediaDownloader,
        CurrencyProvider $currencyProvider
    ) {
        $this->gatewayProvider = $gatewayProvider;
        $this->gatewayCurrencyProvider = $gatewayCurrencyProvider;
        $this->gatewaySalesChannelProvider = $gatewaySalesChannelProvider;
        $this->mediaDownloader = $mediaDownloader;
        $this->currencyProvider = $currencyProvider;
    }

    public function transform(Response $response, BlueMediaContext $context): array
    {
        $data = $response->getData();
        if (!$data instanceof PaywayListResponse) {
            return [];
        }

        $payloads = [];

        /** @var Gateway[] $gateways */
        $gateways = $data->getGateways();

        foreach ($gateways as $gateway) {
            $externalId = $gateway->getGatewayID();

            $internalGatewayId = $this->gatewayProvider->getIdByExternalId(
                $externalId,
                $context->getContext()
            ) ?: Uuid::randomHex();

            $currencyId = $this->currencyProvider->getIdByIso(
                Constants::CURRENCY_PLN,
                $context->getContext()
            );

            $payloads[] = [
                'id' => $internalGatewayId,
                'externalId' => $externalId,
                'name' => $gateway->getGatewayName(),
                'type' => $gateway->getGatewayType(),
                'bankName' => $gateway->getBankName(),
                'logoMediaId' => $this->mediaDownloader->download($gateway->getIconURL(), $context->getContext()),
                'gatewayCurrencies' => [
                    [
                        'id' => $this->gatewayCurrencyProvider->getIdByCurrencyAndGatewayId(
                            $currencyId,
                            $internalGatewayId,
                            $context->getContext()
                        ),
                        'gatewayId' => $internalGatewayId,
                        'currencyId' => $currencyId,
                    ],
                ],
                'gatewaySalesChannels' => [
                    [
                        'id' => $this->gatewaySalesChannelProvider->getIdByGatewayAndSalesChannelId(
                            $internalGatewayId,
                            $context->getSalesChannelId(),
                            $context->getContext()
                        ),
                        'gatewayId' => $internalGatewayId,
                        'salesChannelId' => $context->getSalesChannelId(),
                        'active' => true,
                    ],
                ],
            ];
        }

        return $payloads;
    }
}
