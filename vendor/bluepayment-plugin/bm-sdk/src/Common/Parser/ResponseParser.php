<?php
declare(strict_types=1);

namespace BlueMedia\Common\Parser;

use BlueMedia\Configuration;
use BlueMedia\Common\Util\XMLParser;
use BlueMedia\Common\Enum\ClientEnum;
use Psr\Http\Message\ResponseInterface;
use BlueMedia\Common\Exception\XmlException;

abstract class ResponseParser
{
    /**
     * @var string
     */
    protected $response;

    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(ResponseInterface $response, Configuration $configuration)
    {
        $this->response = (string) $response->getBody();
        $this->configuration = $configuration;
    }

    protected function isErrorResponse(): void
    {
        if (preg_match_all(ClientEnum::PATTERN_XML_ERROR, $this->response, $data)) {
            $xmlData = XMLParser::parse($this->response);

            throw XmlException::xmlBodyContainsError((string) $xmlData->name);
        }

        if (preg_match_all(ClientEnum::PATTERN_GENERAL_ERROR, $this->response, $data)) {
            throw XmlException::xmlGeneralError($this->response);
        }
    }
}
