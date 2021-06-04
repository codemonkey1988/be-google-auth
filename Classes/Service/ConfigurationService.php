<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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
        $extensionConfigurationClassExists = class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class);

        if ($extensionConfigurationClassExists) {
            $extensionConfiguration = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('be_google_auth');
        } else {
            $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_google_auth'] ?? '');
            if (is_array($extensionConfiguration)) {
                $extensionConfiguration = GeneralUtility::removeDotsFromTS($extensionConfiguration);
            }
        }

        if (!is_array($extensionConfiguration)) {
            $extensionConfiguration = [];
        }

        $this->configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class, $extensionConfiguration);
    }
}
