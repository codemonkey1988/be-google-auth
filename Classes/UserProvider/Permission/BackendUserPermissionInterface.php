<?php

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\UserProvider\Permission;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Interface BackendUserPermissionInterface
 */
interface BackendUserPermissionInterface extends SingletonInterface
{
    /**
     * Returns true if the backend user should be an admin.
     *
     * @param string $email
     * @return bool
     */
    public function isAdmin(string $email): bool;

    /**
     * Returns an array of be group uids that should be assigned to the user.
     *
     * @param string $email
     * @return array
     */
    public function getUserGroupUids(string $email): array;
}
