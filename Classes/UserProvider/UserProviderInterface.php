<?php
namespace Codemonkey1988\BeGoogleAuth\UserProvider;

use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;

interface UserProviderInterface
{
    /**
     * @param ConfigurationService $configurationService
     * @return void
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
     * Creates a new user with a configured user group.
     *
     * @param string $email
     * @param string $name
     * @return void
     */
    public function createUser(string $email, string $name);

    /**
     * Sets the deleted flag to 0 for the given user record uid.
     *
     * @param array $userRecord
     * @return void
     */
    public function restoreUser(array $userRecord);
}
