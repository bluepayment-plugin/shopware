<?php
declare(strict_types=1);

namespace BlueMedia\Common\Exception;

final class HashNotReturnedFromServerException extends HashException
{
    public static function noHash(): self
    {
        return new self('No hash received from server! Check your serviceID.');
    }
}
