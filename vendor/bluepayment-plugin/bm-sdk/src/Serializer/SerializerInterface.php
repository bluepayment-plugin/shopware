<?php
declare(strict_types=1);

namespace BlueMedia\Serializer;

use BlueMedia\Common\Dto\AbstractDto;

interface SerializerInterface
{
    public function serializeDataToDto(array $data, string $type): AbstractDto;

    public function toArray(object $object): array;

    public function fromArray(array $data, string $type);

    public function deserializeXml(string $xml, string $type): SerializableInterface;

    public function toXml($data): string;
}
