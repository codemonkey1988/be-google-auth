<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\Service;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ConfigurationServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initializeWithMissingConfiguration()
    {
        $subject = new ConfigurationService();

        self::assertInstanceOf(ExtensionConfiguration::class, $subject->getConfiguration());
    }

    /**
     * @test
     */
    public function initializeWithValidConfiguration()
    {
        $testConfiguration = [
            'clientId' => 'my-client-id',
            'gsuite' => [
                'enable' => '1',
                'organisations' => 'example.com',
                'adminByDefault' => '1',
                'beUserGroupUids' => '1,2,3',
            ],
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_google_auth'] = serialize($testConfiguration);
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_google_auth'] = $testConfiguration;

        $subject = new ConfigurationService();
        $configuration = $subject->getConfiguration();

        self::assertSame('my-client-id', $configuration->getClientId());
        self::assertTrue($configuration->getGsuite()->isEnabled());
        self::assertTrue($configuration->getGsuite()->isAdminByDefault());
        self::assertEquals(['example.com'], $configuration->getGsuite()->getOrganisations());
        self::assertEquals(['1', '2', '3'], $configuration->getGsuite()->getBeUserGroupUids());
    }
}
