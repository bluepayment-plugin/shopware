<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbe7e57adc2a835222c157975263a8a51
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cweagans\\Composer\\' => 18,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'PHPStan\\PhpDocParser\\' => 21,
        ),
        'M' => 
        array (
            'Metadata\\' => 9,
        ),
        'J' => 
        array (
            'JMS\\Serializer\\' => 15,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'D' => 
        array (
            'Doctrine\\Instantiator\\' => 22,
        ),
        'B' => 
        array (
            'BlueMedia\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cweagans\\Composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/cweagans/composer-patches/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
            1 => __DIR__ . '/..' . '/psr/http-factory/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'PHPStan\\PhpDocParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpstan/phpdoc-parser/src',
        ),
        'Metadata\\' => 
        array (
            0 => __DIR__ . '/..' . '/jms/metadata/src',
        ),
        'JMS\\Serializer\\' => 
        array (
            0 => __DIR__ . '/..' . '/jms/serializer/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Doctrine\\Instantiator\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/instantiator/src/Doctrine/Instantiator',
        ),
        'BlueMedia\\' => 
        array (
            0 => __DIR__ . '/..' . '/bluepayment-plugin/bm-sdk/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbe7e57adc2a835222c157975263a8a51::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbe7e57adc2a835222c157975263a8a51::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbe7e57adc2a835222c157975263a8a51::$classMap;

        }, null, ClassLoader::class);
    }
}
