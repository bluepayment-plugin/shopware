<?php
declare(strict_types=1);

namespace Tests\Unit\Common\Parser;

use BlueMedia\Common\Exception\XmlException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Tests\Unit\BaseTestCase;
use BlueMedia\HttpClient\HttpClient;
use BlueMedia\Common\Parser\ServiceResponseParser;

class ServiceResponseParserTest extends BaseTestCase
{
    private $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClient::class);
    }

    /**
     * @dataProvider wrongXmlProvider
     * @param string $response
     */
    public function testParseListResponseThrowsExceptionOnWrongXml(string $response): void
    {
        $parser = new ServiceResponseParser(
            new GuzzleResponse(200, [], $response),
            $this->getConfigurationStub()
        );

        $this->expectException(XmlException::class);

        $parser->parseListResponse('test_class_name');
    }

    public function wrongXmlProvider(): array
    {
        return [
            ['ERROR'],
            ['<error>something</error>']
        ];
    }
}
