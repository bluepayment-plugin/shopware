<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api;

use BlueMedia\Client as BlueMediaClient;
use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Configuration;
use BlueMedia\Hash\HashableInterface;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\ShopwarePayment\Exception\ClientException;
use BlueMedia\Transaction\Builder\TransactionDtoBuilder;
use BlueMedia\Transaction\Dto\TransactionDto;
use Psr\Log\LoggerInterface;
use Throwable;

use function http_build_query;
use function sprintf;

class Client implements ClientInterface
{
    private BlueMediaClient $client;

    private Configuration $configuration;

    private LoggerInterface $apiLogger;

    private string $gatewayUrl;

    public function __construct(
        string $serviceId,
        string $sharedKey,
        string $gatewayUrl,
        LoggerInterface $blueMediaApiLogger,
        string $hashMode = ClientEnum::HASH_SHA256,
        string $hashSeparator = ClientEnum::HASH_SEPARATOR
    ) {
        $this->client = new BlueMediaClient($serviceId, $sharedKey, $hashMode, $hashSeparator);
        $this->configuration = new Configuration($serviceId, $sharedKey, $hashMode, $hashSeparator);
        $this->gatewayUrl = $gatewayUrl;
        $this->apiLogger = $blueMediaApiLogger;
    }

    /**
     * @throws ClientException
     */
    public function getTransactionRedirectUrl(array $transactionData): string
    {
        try {
            $transactionData = $this->ensureGatewayUrlInTransactionData($transactionData);

            /** @var TransactionDto $transactionDto */
            $transactionDto = TransactionDtoBuilder::build($transactionData, $this->configuration);
            $requestData = $transactionDto->getTransaction()->capitalizedArray();

            return sprintf(
                '%s%s?%s',
                $transactionDto->getGatewayUrl(),
                ClientEnum::PAYMENT_ROUTE,
                http_build_query($requestData)
            );
        } catch (Throwable $exception) {
            $this->apiLogger->error(
                sprintf('ERROR on Client::getTransactionRedirectUrl: %s', $exception->getMessage())
            );
            throw new ClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
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
        if (false === isset($transactionData['gatewayUrl'])) {
            $transactionData['gatewayUrl'] = $this->gatewayUrl;
        }

        return $transactionData;
    }
}
