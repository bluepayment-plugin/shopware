<?php
declare(strict_types=1);

namespace Tests\Unit;

use BlueMedia\Configuration;
use BlueMedia\Transaction\Dto\TransactionDtoInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class BaseTestCase extends PhpUnitTestCase
{
    protected const SERVICE_ID = '123456';
    protected const WRONG_SERVICE_ID = '123456789012';
    protected const GATEWAY_URL = 'https://pay-accept.bm.pl';

    protected const SHARED_KEY = 'QCBm3N0oFjzQAWsTIVN8mPLK12TW6HU6InSfjvnF';

    protected const WRONG_HASH_ALGO = 'wrong_algo';
    protected const HASH_SEPARATOR = '|';

    protected const HASH_SHA256 = 'sha256';

    protected function getConfigurationStub(): Stub
    {
        $configuration = $this->createStub(Configuration::class);

        $configuration->method('getServiceId')->willReturn(self::SERVICE_ID);
        $configuration->method('getSharedKey')->willReturn(self::SHARED_KEY);
        $configuration->method('getHashAlgo')->willReturn(self::HASH_SHA256);
        $configuration->method('getHashSeparator')->willReturn(self::HASH_SEPARATOR);

        return $configuration;
    }

    protected function getTransactionDtoStub(): Stub
    {
        $transactionDto = $this->createStub(TransactionDtoInterface::class);

        $transactionDto->method('getOrderId')->willReturn('123-1234');
        $transactionDto->method('getAmount')->willReturn(100.60);
        $transactionDto->method('getDescription')->willReturn('transakcja');
        $transactionDto->method('getGatewayId')->willReturn(0);
        $transactionDto->method('getCurrency')->willReturn('PLN');
        $transactionDto->method('getCustomerEmail')->willReturn('test@test.test');
        $transactionDto->method('getReturnURL')->willReturn('https://google.pl');

        return $transactionDto;
    }
}
