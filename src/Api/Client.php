<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

use BlueMedia\Client as BlueMediaClient;
use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Configuration;
use BlueMedia\Hash\HashableInterface;
use BlueMedia\Hash\HashGenerator;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\ShopwarePayment\Api\DTO\GatewayListDTO;
use BlueMedia\ShopwarePayment\Api\DTO\GooglePayMerchantInfoDTO;
use BlueMedia\ShopwarePayment\Api\Transformer\DtoTransformer;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\ShopwarePayment\Util\Constants;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Throwable;

use function bin2hex;
use function random_bytes;
use function sprintf;

class Client implements ClientInterface
{
    public const PARAM_GATEWAY_URL = 'gatewayUrl';

    public const PARAM_PLATFORM_NAME = 'platformName';

    public const PARAM_PLATFORM_VERSION = 'platformVersion';

    public const PARAM_PLATFORM_PLUGIN_VERSION = 'platformPluginVersion';

    private const HEADER = 'BmHeader';

    private const PAY_HEADER = 'pay-bm';

    private const PLATFORM_NAME = 'Shopware';

    private string $shopwareVersion;

    private string $pluginVersion;

    private string $gatewayUrl;

    private Configuration $configuration;

    private BlueMediaClient $client;

    private LoggerInterface $apiLogger;

    private GuzzleClient $httpClient;

    private DtoTransformer $dtoTransformer;

    public function __construct(
        string $shopwareVersion,
        string $pluginVersion,
        string $serviceId,
        string $sharedKey,
        string $gatewayUrl,
        LoggerInterface $blueMediaApiLogger,
        GuzzleClient $httpClient,
        DtoTransformer $dtoTransformer,
        string $hashMode = ClientEnum::HASH_SHA256,
        string $hashSeparator = ClientEnum::HASH_SEPARATOR
    ) {
        $this->shopwareVersion = $shopwareVersion;
        $this->pluginVersion = $pluginVersion;
        $this->configuration = new Configuration($serviceId, $sharedKey, $hashMode, $hashSeparator);
        $this->client = new BlueMediaClient($serviceId, $sharedKey, $hashMode, $hashSeparator);
        $this->gatewayUrl = $gatewayUrl;
        $this->apiLogger = $blueMediaApiLogger;
        $this->httpClient = $httpClient;
        $this->dtoTransformer = $dtoTransformer;
    }

    /**
     * @throws ClientException
     */
    public function getTransactionRedirect(array $transactionData): Response
    {
        try {
            $transactionData = $this->ensureGatewayUrlInTransactionData($transactionData);

            return $this->client->getTransactionRedirect($transactionData);
        } catch (Throwable $exception) {
            $this->apiLogger->error(
                sprintf('ERROR on Client::getTransactionRedirect: %s', $exception->getMessage())
            );
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function doTransactionBackground(array $transactionData): Response
    {
        try {
            $transactionData = $this->applyStatisticData($transactionData);
            $transactionData = $this->ensureGatewayUrlInTransactionData($transactionData);

            return $this->client->doTransactionBackground($transactionData);
        } catch (Throwable $exception) {
            $this->apiLogger->error(
                sprintf('ERROR on Client::doTransactionBackground: %s', $exception->getMessage())
            );
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function doTransactionInit(array $transactionData): Response
    {
        try {
            $transactionData = $this->applyStatisticData($transactionData);
            $transactionData = $this->ensureGatewayUrlInTransactionData($transactionData);

            return $this->client->doTransactionInit($transactionData);
        } catch (Throwable $exception) {
            $this->apiLogger->error(
                sprintf('ERROR on Client::doTransactionInit: %s', $exception->getMessage())
            );
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function doItnIn(string $itn): Response
    {
        try {
            return $this->client->doItnIn($itn);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::doItnIn: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function doItnInResponse(Itn $itn, bool $transactionConfirmed = true): Response
    {
        try {
            return $this->client->doItnInResponse($itn, $transactionConfirmed);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::doItnInResponse: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function getPaywayList(): Response
    {
        try {
            return $this->client->getPaywayList($this->gatewayUrl);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::getPaywayList: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function getGatewayList(): GatewayListDTO
    {
        try {
            $requestData = [
                'ServiceID' => $this->configuration->getServiceId(),
                'MessageID' => bin2hex(random_bytes(ClientEnum::MESSAGE_ID_LENGTH / 2)),
                'Currencies' => implode(',', Constants::SUPPORTED_CURRENCIES),
            ];

            $hash = HashGenerator::generateHash(
                $requestData,
                $this->configuration
            );

            $requestData['Hash'] = $hash;

            $response = $this->httpClient->post($this->gatewayUrl . BlueMediaRoute::GATEWAY_LIST_ROUTE, [
                RequestOptions::JSON => $requestData,
            ]);

            $decodedResponse = json_decode($response->getBody()->getContents(), true);

            /** @var GatewayListDTO $transformedResponse */
            $transformedResponse = $this->dtoTransformer->transform($decodedResponse, GatewayListDTO::class);

            return $transformedResponse;
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::getGatewayList: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function getGooglePayMerchantInfo(string $domain): GooglePayMerchantInfoDTO
    {
        try {
            $requestData = [
                'ServiceID' => $this->configuration->getServiceId(),
                'MerchantDomain' => $domain,
            ];

            $hash = HashGenerator::generateHash(
                $requestData,
                $this->configuration
            );

            $requestData['Hash'] = $hash;

            $response = $this->httpClient->post($this->gatewayUrl . BlueMediaRoute::GOOGLE_PAY_MERCHANT_INFO_ROUTE, [
                RequestOptions::JSON => $requestData,
                RequestOptions::HEADERS => [self::HEADER => self::PAY_HEADER],
            ]);

            $decodedResponse = json_decode($response->getBody()->getContents(), true);

            /** @var GooglePayMerchantInfoDTO $transformedResponse */
            $transformedResponse = $this->dtoTransformer->transform($decodedResponse, GooglePayMerchantInfoDTO::class);

            return $transformedResponse;
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::getGooglePayMerchantInfo: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function getRegulationList(): Response
    {
        try {
            return $this->client->getRegulationList($this->gatewayUrl);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::getRegulationList: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function checkHash(HashableInterface $data): bool
    {
        try {
            return $this->client->checkHash($data);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::checkHash: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function doConfirmationCheck(array $data): bool
    {
        try {
            return $this->client->doConfirmationCheck($data);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::doConfirmationCheck: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws ClientException
     */
    public function getItnObject(string $itn): Itn
    {
        try {
            return $this->client->getItnObject($itn);
        } catch (Throwable $exception) {
            $this->apiLogger->error(sprintf('ERROR on Client::getItnObject: %s', $exception->getMessage()));
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function ensureGatewayUrlInTransactionData(array $transactionData): array
    {
        if (false === isset($transactionData[self::PARAM_GATEWAY_URL])) {
            $transactionData[self::PARAM_GATEWAY_URL] = $this->gatewayUrl;
        }

        return $transactionData;
    }

    private function applyStatisticData(array $transactionData): array
    {
        $transactionData['transaction'][self::PARAM_PLATFORM_NAME] = self::PLATFORM_NAME;
        $transactionData['transaction'][self::PARAM_PLATFORM_VERSION] = $this->shopwareVersion;
        $transactionData['transaction'][self::PARAM_PLATFORM_PLUGIN_VERSION] = $this->pluginVersion;

        return $transactionData;
    }
}
