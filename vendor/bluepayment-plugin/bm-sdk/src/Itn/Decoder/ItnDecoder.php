<?php
declare(strict_types=1);

namespace BlueMedia\Itn\Decoder;

use BlueMedia\Common\Enum\ClientEnum;

class ItnDecoder
{
    public static function decode(string $itn): string
    {
        $xmlData = base64_decode($itn, true);

        if ($xmlData === false || !preg_match_all(ClientEnum::PATTERN_XML, $xmlData)) {
            throw new \InvalidArgumentException('ITN data must be an valid XML, base64 encoded.');
        }

        return $xmlData;
    }
}
