<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\Google;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Google\Gsuite;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class GsuiteTest extends UnitTestCase
{
    /**
     * @test
     */
    public function validateWithDefaultConfiguration()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        $this->assertFalse($subject->enabled());
        $this->assertFalse($subject->isInOrganisation([]));
        $this->assertFalse($subject->isInOrganisation(['hd' => 'example.com']));
    }

    /**
     * @test
     */
    public function validateWithEnabledGsuite()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([
            'gsuite' => [
                'enable' => true,
            ],
        ]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        $this->assertTrue($subject->enabled());
    }

    /**
     * @test
     */
    public function validateWithValidGsuiteUser()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        $this->assertTrue($subject->isGsuiteUser(['hd' => 'example.com']));
    }

    /**
     * @test
     */
    public function validateWithInvalidGsuiteUser()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        $this->assertFalse($subject->isGsuiteUser([]));
    }

    /**
     * @test
     */
    public function validateUserShouldBeInOrganisation()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([
            'gsuite' => [
                'organisations' => 'example.com',
            ],
        ]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        $this->assertTrue($subject->isInOrganisation(['hd' => 'example.com']));
    }

    /**
     * @param array $extensionConfiguration
     * @return \Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function buildConfigurationServiceMock(array $extensionConfiguration)
    {
        $extensionConfiguration = new ExtensionConfiguration($extensionConfiguration);
        $configurationServiceMock = $this->getAccessibleMock(
            ConfigurationService::class,
            ['getConfiguration']
        );
        $configurationServiceMock
            ->method('getConfiguration')
            ->willReturn($extensionConfiguration);

        return $configurationServiceMock;
    }
}
