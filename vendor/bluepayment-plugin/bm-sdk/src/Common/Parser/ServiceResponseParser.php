<?php
declare(strict_types=1);

namespace BlueMedia\Common\Parser;

use BlueMedia\Serializer\SerializableInterface;
use BlueMedia\Serializer\Serializer;

final class ServiceResponseParser extends ResponseParser
{
    public function parseListResponse(string $type): SerializableInterface
    {
        $this->isErrorResponse();

        return (new Serializer())->deserializeXml($this->response, $type);
    }
}
