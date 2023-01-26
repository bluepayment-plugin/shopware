<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Lifecycle\Common;

trait SerializableTrait
{
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);
        foreach ($vars as $property => $value) {
            $vars[$property] = $value;
        }

        return array_filter($vars);
    }
}
