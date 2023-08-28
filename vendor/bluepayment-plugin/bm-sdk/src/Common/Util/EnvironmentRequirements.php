<?php
declare(strict_types=1);

namespace BlueMedia\Common\Util;

use BlueMedia\Common\Exception\EnviromentRequirementException;

final class EnvironmentRequirements
{
    public const REQUIRED_EXTENSIONS = [
        'xmlwriter',
        'xmlreader',
        'iconv',
        'mbstring',
        'hash'
    ];

    public const PHP_VERSION = 70200;

    /**
     * Checks PHP required environment.
     *
     * @return void
     * @throws EnviromentRequirementException
     */
    public static function checkPhpEnvironment()
    {
        if (!self::hasSupportedPhpVersion()) {
            throw EnviromentRequirementException::phpVersionError();
        }

        foreach (self::REQUIRED_EXTENSIONS as $extensionName) {
            self::hasPhpExtension($extensionName);
        }
    }

    /**
     * @return bool
     */
    public static function hasSupportedPhpVersion(): bool
    {
        return PHP_VERSION_ID >= self::PHP_VERSION;
    }

    /**
     * @param string $extensionName
     */
    public static function hasPhpExtension($extensionName): void
    {
        if (!extension_loaded($extensionName)) {
            throw EnviromentRequirementException::phpExtensionMissing($extensionName);
        }
    }
}
