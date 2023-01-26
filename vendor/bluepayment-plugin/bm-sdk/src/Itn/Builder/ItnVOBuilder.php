<?php
declare(strict_types=1);

namespace BlueMedia\Itn\Builder;

use BlueMedia\Common\Util\XMLParser;
use BlueMedia\Itn\ValueObject\Itn;
use BlueMedia\Serializer\Serializer;

final class ItnVOBuilder
{
    public static function build(string $itnData): Itn
    {
        $xmlData = XMLParser::parse($itnData);

        $xmlTransaction = $xmlData->transactions->transaction->asXML();

        return (new Serializer())->deserializeXml($xmlTransaction, Itn::class);
    }
}
