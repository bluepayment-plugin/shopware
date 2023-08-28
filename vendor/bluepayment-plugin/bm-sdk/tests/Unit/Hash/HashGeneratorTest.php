<?php
declare(strict_types=1);

namespace Tests\Unit\Hash;

use BlueMedia\Hash\HashGenerator;
use Tests\Unit\BaseTestCase;

class HashGeneratorTest extends BaseTestCase
{
    public function testGenerateHashReturnsExpectedHash(): void
    {
        $data = [
            'serviceID' => $this->getConfigurationStub()->getServiceId(),
            'orderID' => 123,
            'amount' => '1.20',
        ];

        $data['hash'] = $this->generateHash($data);

        $hash = HashGenerator::generateHash($data, $this->getConfigurationStub());

        $this->assertSame($data['hash'], $hash);
    }

    private function generateHash(array $data): string
    {
        $configuration = $this->getConfigurationStub();
        $data = implode($configuration->getHashSeparator(), $data);
        $data = sprintf('%s|%s', $data, $configuration->getSharedKey());

        return hash($configuration->getHashAlgo(), $data);
    }
}
