<?php
declare(strict_types=1);

namespace BlueMedia;

use BlueMedia\Confirmation\Builder\ConfirmationVOBuilder;
use BlueMedia\Hash\HashableInterface;
use BlueMedia\HttpClient\HttpClientInterface;
use BlueMedia\Hash\HashChecker;
use BlueMedia\Itn\Builder\ItnVOBuilder;
use BlueMedia\Itn\Decoder\ItnDecoder;
use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\Transaction\View;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\HttpClient\HttpClient;
use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Itn\Builder\ItnDtoBuilder;
use BlueMedia\PaywayList\Dto\PaywayListDto;
use BlueMedia\Itn\Builder\ItnResponseBuilder;
use BlueMedia\HttpClient\ValueObject\Request;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Common\Builder\ServiceDtoBuilder;
use BlueMedia\Common\Parser\ServiceResponseParser;
use BlueMedia\Common\Util\EnvironmentRequirements;
use BlueMedia\RegulationList\Dto\RegulationListDto;
use BlueMedia\Transaction\Builder\TransactionDtoBuilder;
use BlueMedia\Transaction\Parser\TransactionResponseParser;
use BlueMedia\PaywayList\ValueObject\PaywayListResponse\PaywayListResponse;
use BlueMedia\RegulationList\ValueObject\RegulationListResponse\RegulationListResponse;

final class Client
{
    private const HEADER = 'BmHeader';
    private const PAY_HEADER = 'pay-bm';
    private const CONTINUE_HEADER = 'pay-bm-continue-transaction-url';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HttpClientInterface|null
     */
    private $httpClient;

    public function __construct(
        string $serviceId,
        string $sharedKey,
        string $hashMode = ClientEnum::HASH_SHA256,
        string $hashSeparator = ClientEnum::HASH_SEPARATOR,
        HttpClientInterface $httpClient = null
    ) {
        EnvironmentRequirements::checkPhpEnvironment();

        $this->configuration = new Configuration($serviceId, $sharedKey, $hashMode, $hashSeparator);
        $this->httpClient = $httpClient ?? new HttpClient();
    }

    /**
     * Perform standard transaction.
     *
     * @param array $transactionData
     *
     * @return Response
     * @api
     */
    public function getTransactionRedirect(array $transactionData): Response
    {
        return new Response(
            View::createRedirectHtml(TransactionDtoBuilder::build($transactionData, $this->configuration))
        );
    }

    /**
     * Perform transaction in background.
     * Returns payway form or transaction data for user.
     *
     * @param array $transactionData
     *
     * @return Response
     * @api
     */
    public function doTransactionBackground(array $transactionData): Response
    {
        $transactionDto = TransactionDtoBuilder::build($transactionData, $this->configuration);

        $transactionDto->setRequest(new Request(
            $transactionDto->getGatewayUrl() . ClientEnum::PAYMENT_ROUTE,
            [self::HEADER => self::PAY_HEADER]
        ));

        $parser = new TransactionResponseParser($this->httpClient->post($transactionDto), $this->configuration);

        return $parser->parse();
    }

    /**
     * Initialize transaction.
     * Returns transaction continuation or transaction information.
     *
     * @param array $transactionData
     *
     * @return Response
     * @api
     */
    public function doTransactionInit(array $transactionData): Response
    {
        $transactionDto = TransactionDtoBuilder::build($transactionData, $this->configuration);

        $transactionDto->setRequest(new Request(
            $transactionDto->getGatewayUrl() . ClientEnum::PAYMENT_ROUTE,
            [self::HEADER => self::CONTINUE_HEADER]
        ));

        $parser = new TransactionResponseParser($this->httpClient->post($transactionDto), $this->configuration);

        return $parser->parse(true);
    }

    /**
     * Process ITN requests.
     *
     * @param string $itn encoded with base64
     * @return Response
     * @api
     */
    public function doItnIn(string $itn): Response
    {
        $itnDto = ItnDtoBuilder::build(ItnDecoder::decode($itn));

        return new Response($itnDto->getItn());
    }

    /**
     * Returns response for ITN IN request.
     *
     * @param Itn $itn
     * @param bool $transactionConfirmed
     *
     * @return Response
     * @api
     *
     */
    public function doItnInResponse(Itn $itn, bool $transactionConfirmed = true)
    {
        return new Response(ItnResponseBuilder::build($itn, $transactionConfirmed, $this->configuration));
    }

    /**
     * Returns payway list.
     *
     * @param string $gatewayUrl
     * @return Response
     * @api
     */
    public function getPaywayList(string $gatewayUrl): Response
    {
        $paywayListDto = ServiceDtoBuilder::build(
            [
                'gatewayUrl' => $gatewayUrl,
                'paywayList' => [
                    'serviceID' => $this->configuration->getServiceId(),
                    'messageID' => bin2hex(random_bytes(ClientEnum::MESSAGE_ID_LENGTH/2))
                ]
            ],
            PaywayListDto::class,
            $this->configuration
        );

        $paywayListDto->setRequest(new Request(
            $paywayListDto->getGatewayUrl() . ClientEnum::PAYWAY_LIST_ROUTE
        ));

        $parser = new ServiceResponseParser($this->httpClient->post($paywayListDto), $this->configuration);

        return new Response($parser->parseListResponse(PaywayListResponse::class));
    }

    /**
     * Returns payment regulations.
     *
     * @param string $gatewayUrl
     * @return Response
     * @api
     */
    public function getRegulationList(string $gatewayUrl): Response
    {
        $regulationListDto = ServiceDtoBuilder::build(
            [
                'gatewayUrl' => $gatewayUrl,
                'regulationList' => [
                    'serviceID' => $this->configuration->getServiceId(),
                    'messageID' => bin2hex(random_bytes(ClientEnum::MESSAGE_ID_LENGTH/2))
                ]
            ],
            RegulationListDto::class,
            $this->configuration
        );

        $regulationListDto->setRequest(new Request(
            $regulationListDto->getGatewayUrl() . ClientEnum::GET_REGULATIONS_ROUTE
        ));

        $parser = new ServiceResponseParser($this->httpClient->post($regulationListDto), $this->configuration);

        return new Response($parser->parseListResponse(RegulationListResponse::class));
    }

    /**
     * Checks id hash is valid.
     *
     * @param HashableInterface $data
     * @return bool
     * @api
     */
    public function checkHash(HashableInterface $data): bool
    {
        return HashChecker::checkHash($data, $this->configuration);
    }

    /**
     * Method allows to check if gateway returns with valid data.
     *
     * @param array $data
     * @return bool
     * @api
     */
    public function doConfirmationCheck(array $data): bool
    {
        return $this->checkHash(ConfirmationVOBuilder::build($data));
    }

    /**
     * Method allows to get Itn object from base64
     *
     * @param string $itn
     * @return Itn
     */
    public static function getItnObject(string $itn): Itn
    {
        return ItnVOBuilder::build(ItnDecoder::decode($itn));
    }
}
