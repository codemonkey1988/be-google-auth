<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Service;

use TYPO3\CMS\Core\Authentication\AuthenticationService;

/**
 * Class ExtendedAuthenticationService
 */
class ExtendedAuthenticationService extends AuthenticationService
{
    /**
     * Override this function to prevent google oAuth2 users to use the default
     * login mechanism.
     *
     * @param string $username
     * @param string $extraWhere
     * @param string $dbUserSetup
     * @return bool|mixed
     */
    public function fetchUserRecord($username, $extraWhere = '', $dbUserSetup = '')
    {
        $user = parent::fetchUserRecord($username, $extraWhere, $dbUserSetup);

        if (is_array($user) && $user['google_oauth'] === 1) {
            return false;
        }

        return $user;
    }
}
