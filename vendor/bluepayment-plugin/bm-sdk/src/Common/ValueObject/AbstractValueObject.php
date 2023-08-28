<?php
declare(strict_types=1);

namespace BlueMedia\Common\ValueObject;

use BlueMedia\Serializer\Serializer;

abstract class AbstractValueObject
{
    public function toArray(): array
    {
        return (new Serializer())->toArray($this);
    }

    public function capitalizedArray(): array
    {
        $result = $this->toArray();

        return array_combine(
            array_map('ucfirst', array_keys($result)),
            array_values($result)
        );
    }
}
