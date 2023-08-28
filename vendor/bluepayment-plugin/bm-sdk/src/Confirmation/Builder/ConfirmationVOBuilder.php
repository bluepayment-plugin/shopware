<?php
declare(strict_types=1);

namespace BlueMedia\Confirmation\Builder;

use BlueMedia\Serializer\Serializer;
use BlueMedia\Confirmation\ValueObject\Confirmation;

final class ConfirmationVOBuilder
{
    public static function build(array $data): Confirmation
    {
        return (new Serializer())->fromArray($data, Confirmation::class);
    }
}
