<?php
declare(strict_types=1);

namespace Tests\Unit\Common\Helper;

use BlueMedia\Common\Exception\EnviromentRequirementException;
use BlueMedia\Common\Util\EnvironmentRequirements;
use PHPUnit\Framework\TestCase;

class EnvironmentRequirementsTest extends TestCase
{
    public function testHasPhpExtensionReturnsBool(): void
    {
        $this->expectException(EnviromentRequirementException::class);

        EnvironmentRequirements::hasPhpExtension('test_extension');
    }

    public function testHasSupportedPhpVersionReturnsBool(): void
    {
        $result = EnvironmentRequirements::hasSupportedPhpVersion();

        $this->assertIsBool($result);
    }
}
