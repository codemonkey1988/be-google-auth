<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Service;

use TYPO3\CMS\Sv\AuthenticationService;

class ExtendedAuthenticationService extends AuthenticationService
{
    public function fetchUserRecord($username, $extraWhere = '', $dbUserSetup = '')
    {
        $user = parent::fetchUserRecord($username, $extraWhere, $dbUserSetup);

        if (is_array($user) && $user['google_oauth'] === 1) {
            return false;
        }

        return $user;
    }
}
