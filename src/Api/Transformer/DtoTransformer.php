<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Api\Transformer;

use BlueMedia\ShopwarePayment\Api\DTO\ResponseDTOInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Shopware\Core\Framework\Struct\Collection;

class DtoTransformer
{
    /**
     * @throws RuntimeException
     * @throws ReflectionException
     */
    public function transform(array $data, string $class): ResponseDTOInterface
    {
        if (false === class_exists($class)) {
            throw new RuntimeException(sprintf('%s class does not exist.', $class));
        }

        $dto = new $class();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $refl = new ReflectionProperty($class, $key);
                $collectionType = $refl->getType()->getName();

                if (false === is_a($collectionType, Collection::class, true)) {
                    continue;
                }

                $method = new ReflectionMethod($collectionType, 'getExpectedClass');
                $method->setAccessible(true);
                $nestedType = $method->invoke(new $collectionType());

                foreach ($value as &$nestedValue) {
                    $nestedValue = $this->transform($nestedValue, $nestedType);
                }
            }

            $setter = sprintf('set%s', ucfirst($key));
            $dto->$setter(is_array($value) ? new $collectionType($value) : $value);
        }

        return $dto;
    }
}
