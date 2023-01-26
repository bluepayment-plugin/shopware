<?php
declare(strict_types=1);

namespace BlueMedia\Itn\Builder;

use BlueMedia\Itn\Dto\ItnDto;
use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\Serializer\Serializer;
use BlueMedia\Common\Util\XMLParser;

final class ItnDtoBuilder
{
    public static function build(string $itnData): SerializableInterface
    {
        $serializer = new Serializer();
        $xmlData = XMLParser::parse($itnData);

        $xmlTransaction = $xmlData->transactions->transaction->asXML();
        $itnDto = $serializer->deserializeXml($xmlTransaction, ItnDto::class);

        $itnDto->getItn()->setServiceID((string) $xmlData->serviceID);
        $itnDto->getItn()->setHash((string) $xmlData->hash);

        return $itnDto;
    }
}
