<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\Domain\Model\Dto;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\GsuiteConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ExtensionConfigurationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initializeWithEmptyConfiguration()
    {
        $subject = new ExtensionConfiguration([]);

        $this->assertSame('', $subject->getClientId());
        $this->assertInstanceOf(GsuiteConfiguration::class, $subject->getGsuite());
    }

    /**
     * @test
     */
    public function initializeWithClientIdConfiguration()
    {
        $subject = new ExtensionConfiguration([
            'clientId' => '12345',
        ]);

        $this->assertSame('12345', $subject->getClientId());
    }
}
