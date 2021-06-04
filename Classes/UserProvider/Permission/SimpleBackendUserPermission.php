<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\UserProvider\Permission;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SimpleBackendUserPermission
 */
class SimpleBackendUserPermission implements BackendUserPermissionInterface
{
    /**
     * @inheritdoc
     */
    public function isAdmin(string $email): bool
    {
        return $this->getConfiguration()->getGsuite()->isAdminByDefault();
    }

    /**
     * @inheritdoc
     */
    public function getUserGroupUids(string $email): array
    {
        return $this->getConfiguration()->getGsuite()->getBeUserGroupUids();
    }

    /**
     * @return ExtensionConfiguration
     */
    protected function getConfiguration(): ExtensionConfiguration
    {
        return GeneralUtility::makeInstance(ConfigurationService::class)->getConfiguration();
    }
}
