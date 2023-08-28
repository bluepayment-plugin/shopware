<?php
declare(strict_types=1);

namespace BlueMedia\Hash;

use BlueMedia\Common\Exception\HashNotReturnedFromServerException;
use BlueMedia\Configuration;

final class HashChecker
{
    public static function checkHash(HashableInterface $data, Configuration $configuration): bool
    {
        if (!$data->isHashPresent()) {
            throw HashNotReturnedFromServerException::noHash();
        }

        $dataHash = HashGenerator::generateHash(
            $data->toArray(),
            $configuration
        );

        return $dataHash === $data->getHash();
    }
}
