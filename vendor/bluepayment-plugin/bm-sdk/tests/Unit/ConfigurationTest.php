<?php
declare(strict_types=1);

namespace Tests\Unit;

use BlueMedia\Configuration;

class ConfigurationTest extends BaseTestCase
{
    private $configuration;

    protected function setUp(): void
    {
        $this->configuration = new Configuration(
            parent::SERVICE_ID,
            parent::SHARED_KEY,
            parent::HASH_SHA256,
            parent::HASH_SEPARATOR
        );
    }

    public function testThrowsExceptionOnInvalidHashAlgo(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Configuration(
            self::SERVICE_ID,
            self::SHARED_KEY,
            self::WRONG_HASH_ALGO,
            self::HASH_SEPARATOR
        );
    }

    public function testThrowsExceptionOnInvalidServiceId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Configuration(
            self::WRONG_SERVICE_ID,
            self::SHARED_KEY,
            self::HASH_SHA256,
            self::HASH_SEPARATOR
        );
    }

    public function testGetServiceIdReturnTypeAndValue(): void
    {
        $this->assertSame(self::SERVICE_ID, $this->configuration->getServiceId());
    }

    public function testGetSharedKeyReturnTypeAndValue(): void
    {
        $this->assertSame(self::SHARED_KEY, $this->configuration->getSharedKey());
    }

    public function testGetHashAlgoReturnTypeAndValue(): void
    {
        $this->assertSame(self::HASH_SHA256, $this->configuration->getHashAlgo());
    }

    public function testGetHashSeparatorReturnTypeAndValue(): void
    {
        $this->assertSame(self::HASH_SEPARATOR, $this->configuration->getHashSeparator());
    }
}
