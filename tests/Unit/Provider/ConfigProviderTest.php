<?php

declare(strict_types=1);

namespace BlueMedia\Tests\Unit\Provider;

use BlueMedia\ShopwarePayment\Provider\ConfigProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

use function sprintf;

class ConfigProviderTest extends TestCase
{
    private const CONFIG_PATTERN = 'BlueMediaShopwarePayment.config.%s';
    private const SALES_CHANNEL_ID = '5cc5f4d71c6e24832b791adbb30112427';
    private const CLIENT_ID = 2342423423;
    private const SHARED_KEY = 'ASasdDWEfBdfwe!@3123';
    private const HASH_ALGO = 'sha256';
    private const CUSTOM_HASH_ALGO = 'md5';
    private const TEST_GATEWAY_URL = 'https://test.test/';
    private const GATEWAY_URL = 'https://live.live/';

    /**
     * @var Stub|SystemConfigService|null
     */
    private $systemConfigServiceStub = null;

    /**
     * @var Stub|EntityRepositoryInterface|null
     */
    private $entityRepositoryInterfaceDummy = null;

    protected function setUp(): void
    {
        $this->systemConfigServiceStub = $this->createStub(SystemConfigService::class);
        $this->entityRepositoryInterfaceDummy = $this->createStub(EntityRepositoryInterface::class);
    }

    public function testIsEnabled(): void
    {
        $this->systemConfigServiceStub
            ->method('getBool')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'enabled'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'enabled'), null],
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $provider = new ConfigProvider($this->systemConfigServiceStub, $this->entityRepositoryInterfaceDummy);

        Assert::assertTrue($provider->isEnabled(self::SALES_CHANNEL_ID));
        Assert::assertFalse($provider->isEnabled());
    }

    public function testGetServiceId(): void
    {
        $this->systemConfigServiceStub
            ->method('getInt')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'serviceId'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'serviceId'), null],
            )
            ->willReturnOnConsecutiveCalls(self::CLIENT_ID, 0);

        $provider = new ConfigProvider($this->systemConfigServiceStub, $this->entityRepositoryInterfaceDummy);

        Assert::assertEquals(self::CLIENT_ID, $provider->getServiceId(self::SALES_CHANNEL_ID));
        Assert::assertEquals(0, $provider->getServiceId());
    }

    public function testGetSharedKey(): void
    {
        $this->systemConfigServiceStub
            ->method('getString')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'sharedKey'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'sharedKey'), null],
            )
            ->willReturnOnConsecutiveCalls(self::SHARED_KEY, '');

        $provider = new ConfigProvider($this->systemConfigServiceStub, $this->entityRepositoryInterfaceDummy);

        Assert::assertEquals(self::SHARED_KEY, $provider->getSharedKey(self::SALES_CHANNEL_ID));
        Assert::assertEquals('', $provider->getSharedKey());
    }

    public function testGetHashAlgorithm(): void
    {
        $this->systemConfigServiceStub
            ->method('getString')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'hashAlgo'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'hashAlgo'), null],
            )
            ->willReturnOnConsecutiveCalls(self::CUSTOM_HASH_ALGO, self::HASH_ALGO);

        $provider = new ConfigProvider($this->systemConfigServiceStub, $this->entityRepositoryInterfaceDummy);

        Assert::assertEquals(self::CUSTOM_HASH_ALGO, $provider->getHashAlgorithm(self::SALES_CHANNEL_ID));
        Assert::assertEquals(self::HASH_ALGO, $provider->getHashAlgorithm());
    }

    public function testGetGatewayUrl(): void
    {
        $systemConfigServiceMock = $this->createMock(SystemConfigService::class);

        $systemConfigServiceMock
            ->expects($this->exactly(2))
            ->method('getBool')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'testMode'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'testMode'), null],
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $systemConfigServiceMock
            ->expects($this->exactly(2))
            ->method('getString')
            ->withConsecutive(
                [sprintf(self::CONFIG_PATTERN, 'testGatewayUrl'), self::SALES_CHANNEL_ID],
                [sprintf(self::CONFIG_PATTERN, 'gatewayUrl'), null],
            )
            ->willReturnOnConsecutiveCalls(self::TEST_GATEWAY_URL, self::GATEWAY_URL);

        $provider = new ConfigProvider($systemConfigServiceMock, $this->entityRepositoryInterfaceDummy);

        Assert::assertEquals(self::TEST_GATEWAY_URL, $provider->getGatewayUrl(self::SALES_CHANNEL_ID));
        Assert::assertEquals(self::GATEWAY_URL, $provider->getGatewayUrl());
    }

    protected function tearDown(): void
    {
        $this->systemConfigServiceStub = null;
        $this->entityRepositoryInterfaceDummy = null;
    }
}
