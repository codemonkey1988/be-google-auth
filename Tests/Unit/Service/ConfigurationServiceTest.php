<?php
declare(strict_types=1);
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

        $this->assertInstanceOf(ExtensionConfiguration::class, $subject->getConfiguration());
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

        $this->assertSame('my-client-id', $configuration->getClientId());
        $this->assertTrue($configuration->getGsuite()->isEnabled());
        $this->assertTrue($configuration->getGsuite()->isAdminByDefault());
        $this->assertEquals(['example.com'], $configuration->getGsuite()->getOrganisations());
        $this->assertEquals(['1', '2', '3'], $configuration->getGsuite()->getBeUserGroupUids());
    }
}
