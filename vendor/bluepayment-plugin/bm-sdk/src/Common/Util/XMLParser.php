<?php
declare(strict_types=1);

namespace BlueMedia\Common\Util;

use BlueMedia\Common\Exception\XmlException;
use SimpleXMLElement;

final class XMLParser
{
    public static function parse($xml)
    {
        try {
            return new SimpleXMLElement($xml);
        } catch (\Throwable $exception) {
            throw XmlException::xmlParseError($exception);
        }
    }
}
