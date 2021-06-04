<?php

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\UserProvider;

use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;

/**
 * Interface UserProviderInterface
 */
interface UserProviderInterface
{
    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService);

    /**
     * Get a user by its email address.
     *
     * @param string $email
     * @param bool $respectEnableFields
     * @return array
     */
    public function getUserByEmail(string $email, $respectEnableFields = true): array;

    /**
     * Creates a new user with a defined privileges.
     *
     * @param string $email
     * @param string $name
     */
    public function createUser(string $email, string $name);

    /**
     * Sets the deleted flag to 0 for the given user record.
     *
     * @param array $userRecord
     */
    public function restoreUser(array $userRecord);
}
