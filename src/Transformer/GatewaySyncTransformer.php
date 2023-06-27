<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Transformer;

use BlueMedia\ShopwarePayment\Api\DTO\GatewayCurrencyDTO;
use BlueMedia\ShopwarePayment\Api\DTO\GatewayDTO;
use BlueMedia\ShopwarePayment\Api\DTO\GatewayListDTO;
use BlueMedia\ShopwarePayment\Context\BlueMediaContext;
use BlueMedia\ShopwarePayment\Provider\CurrencyProvider;
use BlueMedia\ShopwarePayment\Provider\GatewayCurrencyProvider;
use BlueMedia\ShopwarePayment\Provider\GatewayProvider;
use BlueMedia\ShopwarePayment\Service\MediaDownloader;
use Closure;
use Shopware\Core\Framework\Uuid\Uuid;

class GatewaySyncTransformer
{
    private const CACHE_INTERNAL_GATEWAY_ID = 'internalGatewayId';
    private const CACHE_INTERNAL_GATEWAY_CURRENCY_ID = 'internalGatewayCurrencyId';
    private const CACHE_CURRENCY_ID = 'currencyId';

    private array $memCache = [];

    private GatewayProvider $gatewayProvider;

    private GatewayCurrencyProvider $gatewayCurrencyProvider;

    private MediaDownloader $mediaDownloader;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        GatewayProvider $gatewayProvider,
        GatewayCurrencyProvider $gatewayCurrencyProvider,
        MediaDownloader $mediaDownloader,
        CurrencyProvider $currencyProvider
    ) {
        $this->gatewayProvider = $gatewayProvider;
        $this->gatewayCurrencyProvider = $gatewayCurrencyProvider;
        $this->mediaDownloader = $mediaDownloader;
        $this->currencyProvider = $currencyProvider;
    }

    public function transform(GatewayListDTO $gatewayList, BlueMediaContext $context): array
    {
        $this->memCache = [];
        $payloads = [];

        /** @var GatewayDTO $gateway */
        foreach ($gatewayList->getGatewayList() as $gateway) {
            $payloads[] = [
                'id' => $this->getCachedInternalGatewayId($gateway->getGatewayID(), $context),
                'gatewayId' => $gateway->getGatewayID(),
                'name' => $gateway->getGatewayName(),
                'description' => $gateway->getGatewayDescription(),
                'type' => $gateway->getGatewayType(),
                'bankName' => $gateway->getBankName(),
                'logoMediaId' => $this->mediaDownloader->download($gateway->getIconURL(), $context->getContext()),
                'currencies' => $this->extractCurrencies($gateway, $context),
                'salesChannelsActive' => $this->extractActive($gateway, $context),
            ];
        }

        return $payloads;
    }

    private function extractCurrencies(GatewayDTO $gateway, BlueMediaContext $context): array
    {
        $payloads = [];

        /** @var GatewayCurrencyDTO $currency */
        foreach ($gateway->getCurrencyList() as $currency) {
            $internalGatewayId = $this->getCachedInternalGatewayId($gateway->getGatewayID(), $context);
            $currencyId = $this->getCachedCurrencyId($currency->getCurrency(), $context);
            if (null === $currencyId) {
                continue;
            }

            $payloads[] = [
                'id' => $this->getCachedInternalGatewayCurrencyId($currencyId, $internalGatewayId, $context),
                'gatewayId' => $internalGatewayId,
                'currencyId' => $currencyId,
                'minCartAmount' => $currency->getMinAmount(),
                'maxCartAmount' => $currency->getMaxAmount(),
            ];
        }

        return $payloads;
    }

    private function extractActive(GatewayDTO $gateway, BlueMediaContext $context): array
    {
        if (GatewayDTO::STATE_OK !== $gateway->getState()) {
            return [];
        }

        return [
            [
                'id' => $context->getSalesChannelId(),
            ],
        ];
    }

    private function getCachedInternalGatewayId(int $gatewayId, BlueMediaContext $context): string
    {
        return $this->getCachedId(
            self::CACHE_INTERNAL_GATEWAY_ID,
            (string) $gatewayId,
            function () use ($gatewayId, $context) {
                return $this->gatewayProvider->getIdByGatewayId(
                    $gatewayId,
                    $context->getContext()
                ) ?: Uuid::randomHex();
            }
        );
    }

    private function getCachedCurrencyId(string $currencyIso, BlueMediaContext $context): ?string
    {
        return $this->getCachedId(
            self::CACHE_CURRENCY_ID,
            $currencyIso,
            function () use ($currencyIso, $context) {
                return $this->currencyProvider->getIdByIso(
                    $currencyIso,
                    $context->getContext()
                );
            }
        );
    }

    private function getCachedInternalGatewayCurrencyId(
        string $currencyId,
        string $internalGatewayId,
        BlueMediaContext $context
    ): string {
        return $this->getCachedId(
            self::CACHE_INTERNAL_GATEWAY_CURRENCY_ID,
            sprintf('%s_%s', $currencyId, $internalGatewayId),
            function () use ($currencyId, $internalGatewayId, $context) {
                return $this->gatewayCurrencyProvider->getIdByCurrencyAndGatewayId(
                    $currencyId,
                    $internalGatewayId,
                    $context->getContext()
                ) ?: Uuid::randomHex();
            }
        );
    }

    private function getCachedId(string $cacheGroup, string $cacheKey, Closure $getter): ?string
    {
        if (isset($this->memCache[$cacheGroup][$cacheKey])) {
            return $this->memCache[$cacheGroup][$cacheKey];
        }

        return $this->memCache[$cacheGroup][$cacheKey] = $getter();
    }
}
