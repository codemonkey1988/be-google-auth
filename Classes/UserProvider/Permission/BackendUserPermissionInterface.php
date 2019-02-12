<?php
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
