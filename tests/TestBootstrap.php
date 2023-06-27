<?php

declare(strict_types=1);

namespace BlueMedia\Tests;

// phpcs:disable
function getProjectDir(): string
{
    if (isset($_SERVER['PROJECT_ROOT']) && file_exists($_SERVER['PROJECT_ROOT'])) {
        return $_SERVER['PROJECT_ROOT'];
    }
    if (isset($_ENV['PROJECT_ROOT']) && file_exists($_ENV['PROJECT_ROOT'])) {
        return $_ENV['PROJECT_ROOT'];
    }

    if (file_exists('vendor')) {
        return (string)getcwd();
    }

    $dir = $rootDir = __DIR__;
    while (!file_exists($dir . '/vendor')) {
        if ($dir === dirname($dir)) {
            return $rootDir;
        }
        $dir = dirname($dir);
    }

    return $dir;
}

define('TEST_PROJECT_DIR', getProjectDir());
$loader = require_once TEST_PROJECT_DIR . '/vendor/autoload.php';

// This is just to get the tests running within this plugin. More robust solution is needed for project testing
$loader->addPsr4('BlueMedia\\ShopwarePayment\\', TEST_PROJECT_DIR . '/custom/static-plugins/BlueMediaShopwarePayment/src', true);
$loader->addPsr4('BlueMedia\\Tests\\', TEST_PROJECT_DIR . '/custom/static-plugins/BlueMediaShopwarePayment/tests', true);
$loader->register();
