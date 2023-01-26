<?php
declare(strict_types=1);

namespace Tests\Unit;

use BlueMedia\Common\Enum\ClientEnum;
use BlueMedia\Common\Exception\HashNotReturnedFromServerException;
use BlueMedia\HttpClient\HttpClient;
use BlueMedia\Transaction\ValueObject\Transaction;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Tests\Fixtures\Transaction as TransactionFixtures;
use Tests\Fixtures\PaywayList as PaywayListFixtures;
use Tests\Fixtures\RegulationList as RegulationListFixtures;
use Tests\Fixtures\Itn as ItnFixtures;
use BlueMedia\Client;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\HttpClient\ValueObject\Response;
use BlueMedia\Itn\ValueObject\ItnResponse\ItnResponse;
use BlueMedia\RegulationList\ValueObject\RegulationListResponse\RegulationListResponse;
use BlueMedia\Transaction\ValueObject\TransactionInit;
use BlueMedia\Transaction\ValueObject\TransactionContinue;
use BlueMedia\Transaction\ValueObject\TransactionBackground;
use BlueMedia\PaywayList\ValueObject\PaywayListResponse\PaywayListResponse;

class ClientTest extends BaseTestCase
{
    private $client;

    private $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClient::class);

        $this->client = new Client(
            parent::SERVICE_ID,
            parent::SHARED_KEY,
            ClientEnum::HASH_SHA256,
            ClientEnum::HASH_SEPARATOR,
            $this->httpClient
        );
    }

    public function testGetTransactionRedirectReturnsRedirectView(): void
    {
        $result = $this->client
            ->getTransactionRedirect(TransactionFixtures\TransactionInit::getTransactionInitContinue());

        $this->assertInstanceOf(Response::class, $result);
        $this->assertIsString(Response::class, $result->getData());
    }

    /**
     * @dataProvider checkConfirmationProvider
     * @param array $data
     * @param bool $result
     */
    public function testDoConfirmationCheckRetrurnsStatus(array $data, bool $result): void
    {
        $check = $this->client->doConfirmationCheck($data);

        $this->assertSame($result, $check);
    }

    public function testDoTransactionBackgroundReturnsTransactionData(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], TransactionFixtures\TransactionBackground::getTransactionBackgroundResponse())
        );

        $result = $this->client
            ->doTransactionBackground(TransactionFixtures\TransactionBackground::getTransactionBackground());

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(TransactionBackground::class, $result->getData());

        $transactionBackground = $result->getData();
        $transactionBackgroundFixture =
            TransactionFixtures\TransactionBackground::getTransactionBackgroundResponseData();

        $this->assertSame($transactionBackgroundFixture['receiverNRB'], $transactionBackground->getReceiverNRB());
        $this->assertSame($transactionBackgroundFixture['receiverName'], $transactionBackground->getReceiverName());
        $this->assertSame(
            $transactionBackgroundFixture['receiverAddress'],
            $transactionBackground->getReceiverAddress()
        );
        $this->assertSame($transactionBackgroundFixture['orderID'], $transactionBackground->getOrderID());
        $this->assertSame($transactionBackgroundFixture['amount'], $transactionBackground->getAmount());
        $this->assertSame($transactionBackgroundFixture['currency'], $transactionBackground->getCurrency());
        $this->assertSame($transactionBackgroundFixture['title'], $transactionBackground->getTitle());
        $this->assertSame($transactionBackgroundFixture['remoteID'], $transactionBackground->getRemoteID());
        $this->assertSame($transactionBackgroundFixture['bankHref'], $transactionBackground->getBankHref());
        $this->assertSame($transactionBackgroundFixture['returnURL'], $transactionBackground->getReturnURL());
    }

    public function testDoTransactionBackgroundReturnsPaywayForm(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], TransactionFixtures\TransactionBackground::getPaywayFormResponse())
        );

        $result = $this->client
            ->doTransactionBackground(TransactionFixtures\TransactionBackground::getTransactionBackground());

        $this->assertInstanceOf(Response::class, $result);
        $this->assertIsString($result->getData());
    }

    public function testDoTransactionInitReturnsTransactionContinueData(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], TransactionFixtures\TransactionInit::getTransactionInitContinueResponse())
        );

        $result = $this->client
            ->doTransactionInit(TransactionFixtures\TransactionInit::getTransactionInitContinue());

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(TransactionContinue::class, $result->getData());
    }

    public function testDoTransactionInitReturnsTransactionData(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], TransactionFixtures\TransactionInit::getTransactionInitResponse())
        );

        $result = $this->client
            ->doTransactionInit(TransactionFixtures\TransactionInit::getTransactionInit());

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(TransactionInit::class, $result->getData());
    }

    public function testDoItnInReturnsItnData(): void
    {
        $result = $this->client->doItnIn(ItnFixtures\Itn::getItnInRequest());

        $itn = $result->getData();
        $itnFixture = (array) ItnFixtures\Itn::getTransactionXml();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(Itn::class, $result->getData());
        $this->assertSame($itnFixture['remoteID'], $itn->getRemoteId());
        $this->assertSame($itnFixture['amount'], $itn->getAmount());
        $this->assertSame($itnFixture['currency'], $itn->getCurrency());
        $this->assertSame($itnFixture['paymentDate'], $itn->getPaymentDate());
        $this->assertSame($itnFixture['paymentStatus'], $itn->getPaymentStatus());
        $this->assertSame($itnFixture['paymentStatusDetails'], $itn->getPaymentStatusDetails());
    }

    /**
     * @dataProvider itnProvider
     * @param string $itn
     */
    public function testDoItnInThrowsExceptionOnWrongBase64($itn): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->client->doItnIn($itn);
    }

    public function testDoItnResponseReturnsConfirmationResponse(): void
    {
        $itnIn = $this->client->doItnIn(ItnFixtures\Itn::getItnInRequest());
        $result = $this->client->doItnInResponse($itnIn->getData(), true);
        $this->assertInstanceOf(ItnResponse::class, $result->getData());
        $this->assertXmlStringEqualsXmlString(ItnFixtures\Itn::getItnResponse(), $result->getData()->toXml());
    }

    public function testGetPaywayList(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], PaywayListFixtures\PaywayList::getPaywayListResponse())
        );

        $result = $this->client->getPaywayList(parent::GATEWAY_URL);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(PaywayListResponse::class, $result->getData());
    }

    public function testGetRegulationList(): void
    {
        $this->httpClient->expects($this->once())->method('post')->withAnyParameters()->willReturn(
            new GuzzleResponse(200, [], RegulationListFixtures\RegulationList::getRegulationListResponse())
        );

        $result = $this->client->getRegulationList(parent::GATEWAY_URL);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertInstanceOf(RegulationListResponse::class, $result->getData());
    }

    /**
     * @dataProvider checkHashProvider
     * @param string $hash
     * @param bool $value
     */
    public function testCheckHashReturnsExpectedValue(string $hash, bool $value): void
    {
        $transactionStub = $this->createStub(Transaction::class);
        $transactionInitData = TransactionFixtures\TransactionInit::getTransactionInit();

        $transactionStub->method('toArray')->willReturn($transactionInitData);
        $transactionStub->method('getHash')->willReturn($hash);
        $transactionStub->method('isHashPresent')->willReturn(true);

        $result = $this->client->checkHash($transactionStub);

        $this->assertSame($value, $result);
    }

    /**
     * @dataProvider checkHashProvider
     * @param string $hash
     * @param bool $value
     */
    public function testCheckHashThrowsHashNotReturnedException(string $hash, bool $value): void
    {
        $transactionStub = $this->createStub(Transaction::class);
        $transactionInitData = TransactionFixtures\TransactionInit::getTransactionInit();

        $transactionStub->method('toArray')->willReturn($transactionInitData);
        $transactionStub->method('getHash')->willReturn($hash);
        $transactionStub->method('isHashPresent')->willReturn(false);

        $this->expectException(HashNotReturnedFromServerException::class);

        $this->client->checkHash($transactionStub);
    }

    public function testGetItnObject(): void
    {
        $itn = $this->client::getItnObject(ItnFixtures\Itn::getItnInRequest());
        $itnFixture = (array) ItnFixtures\Itn::getTransactionXml();

        $this->assertInstanceOf(Itn::class, $itn);
        $this->assertSame($itnFixture['remoteID'], $itn->getRemoteId());
        $this->assertSame($itnFixture['amount'], $itn->getAmount());
        $this->assertSame($itnFixture['currency'], $itn->getCurrency());
        $this->assertSame($itnFixture['paymentDate'], $itn->getPaymentDate());
        $this->assertSame($itnFixture['paymentStatus'], $itn->getPaymentStatus());
        $this->assertSame($itnFixture['paymentStatusDetails'], $itn->getPaymentStatusDetails());
    }

    public function checkHashProvider(): array
    {
        return [
            ['56507c9294e43e649e8726d271174297a165aedb858edb0414aadbc9632f17e7', true],
            ['56507c9294e43e649e8726d271174297a165aedb858edb0414aadbc9632f1111', false]
        ];
    }

    public function itnProvider(): array
    {
        return [
            ['PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiP'],
            ['nope'],
            ['']
        ];
    }

    public function checkConfirmationProvider(): array
    {
        return [
            [
                [
                    'ServiceID' => parent::SERVICE_ID,
                    'OrderID' => '123',
                    'Hash' => 'df5f737f48bcef93361f590b460cc633b28f91710a60415527221f9cb90da52a'
                ],
                true
            ],
            [
                [
                    'ServiceID' => parent::SERVICE_ID,
                    'OrderID' => '123',
                    'Hash' => 'cd4dd0eed6bfeb1fd0605076caf7b7774624af7cb67cd63b97425c26471d8100'
                ],
                false
            ],
        ];
    }
}
