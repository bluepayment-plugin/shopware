<?php
declare(strict_types=1);

namespace Tests\Unit\Common\Util;

use BlueMedia\Common\Exception\XmlException;
use BlueMedia\Common\Util\XMLParser;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Itn\Itn;

class XMLParserTest extends TestCase
{
    public function testParseReturnsSimpleXmlElement(): void
    {
        $xmlElement = XMLParser::parse(Itn::getItnResponse());

        $this->assertInstanceOf(\SimpleXMLElement::class, $xmlElement);
        $this->assertXmlStringEqualsXmlString(Itn::getItnResponse(), $xmlElement->asXML());
    }

    /**
     * @param string $wrongXml
     * @dataProvider wrongXmlProvider
     */
    public function testParseThrowsErrorOnWrongXml(string $wrongXml): void
    {
        $this->expectException(XmlException::class);

        XMLParser::parse($wrongXml);
    }

    public function wrongXmlProvider(): array
    {
        return [
            ['ERROR'],
            [''],
            ['wrong_xml'],
            ['<?xml version="1.0" encoding="UTF-8"?>']
        ];
    }
}
