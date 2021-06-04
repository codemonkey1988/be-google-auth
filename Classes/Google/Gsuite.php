<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Google;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class Gsuite
 */
class Gsuite implements SingletonInterface
{
    /**
     * @var ExtensionConfiguration
     */
    protected $configuration;

    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configuration = $configurationService->getConfiguration();
    }

    /**
     * Returns true if the gsuite usage is enabled, otherwise false.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->configuration->getGsuite()->isEnabled();
    }

    /**
     * Returns true if the given user is a gsuite user, otherwise false.
     *
     * @see \Codemonkey1988\BeGoogleAuth\Google\Client::fetchUserProfile
     * @param array $userData
     * @return bool
     */
    public function isGsuiteUser(array $userData): bool
    {
        return !empty($userData['hd']);
    }

    /**
     * Returns true is the given user belongs to a configured organisation. Otherwise it returns false.
     *
     * @see \Codemonkey1988\BeGoogleAuth\Google\Client::fetchUserProfile
     * @param array $userData
     * @return bool
     */
    public function isInOrganisation(array $userData): bool
    {
        if (empty($userData['hd'])) {
            return false;
        }

        return in_array($userData['hd'], $this->configuration->getGsuite()->getOrganisations());
    }
}
