<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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

        self::assertFalse($subject->enabled());
        self::assertFalse($subject->isInOrganisation([]));
        self::assertFalse($subject->isInOrganisation(['hd' => 'example.com']));
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

        self::assertTrue($subject->enabled());
    }

    /**
     * @test
     */
    public function validateWithValidGsuiteUser()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        self::assertTrue($subject->isGsuiteUser(['hd' => 'example.com']));
    }

    /**
     * @test
     */
    public function validateWithInvalidGsuiteUser()
    {
        $configurationServiceMock = $this->buildConfigurationServiceMock([]);

        $subject = new Gsuite();
        $subject->injectConfigurationService($configurationServiceMock);

        self::assertFalse($subject->isGsuiteUser([]));
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

        self::assertTrue($subject->isInOrganisation(['hd' => 'example.com']));
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
