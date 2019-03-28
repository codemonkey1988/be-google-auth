<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Service;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigurationService
 */
class ConfigurationService implements SingletonInterface
{
    /**
     * @var ExtensionConfiguration
     */
    protected $configuration;

    /**
     * Returns the current extension configuration.
     *
     * @return ExtensionConfiguration
     */
    public function getConfiguration(): ExtensionConfiguration
    {
        if (empty($this->configuration)) {
            $this->loadConfiguration();
        }

        return $this->configuration;
    }

    protected function loadConfiguration()
    {
        if (version_compare(TYPO3_version, '9.5.0', '<')) {
            $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_google_auth'] ?? '');
            if (is_array($extensionConfiguration)) {
                $extensionConfiguration = GeneralUtility::removeDotsFromTS($extensionConfiguration);
            }
        } else {
            $extensionConfiguration = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('be_google_auth');
        }

        if (!is_array($extensionConfiguration)) {
            $extensionConfiguration = [];
        }

        $this->configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class, $extensionConfiguration);
    }
}
