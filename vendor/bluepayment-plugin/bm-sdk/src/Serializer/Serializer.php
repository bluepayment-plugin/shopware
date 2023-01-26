<?php
declare(strict_types=1);

namespace BlueMedia\Serializer;

use BlueMedia\Common\Dto\AbstractDto;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

final class Serializer implements SerializerInterface
{
    private const XML_TYPE = 'xml';

    private $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->build();
    }

    public function serializeDataToDto(array $data, string $type): AbstractDto
    {
        return $this->serializer->fromArray($data, $type);
    }

    public function toArray(object $object): array
    {
        return $this->serializer->toArray($object);
    }

    public function fromArray(array $data, string $type)
    {
        return $this->serializer->fromArray($data, $type);
    }

    public function deserializeXml(string $xml, string $type): SerializableInterface
    {
        return $this->serializer->deserialize($xml, $type, self::XML_TYPE);
    }

    public function toXml($data): string
    {
        return $this->serializer->serialize($data, self::XML_TYPE);
    }
}
