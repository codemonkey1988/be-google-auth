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
 * Class AdminByFileBackendUserPermission
 */
class AdminByFileBackendUserPermission implements BackendUserPermissionInterface
{
    /**
     * @inheritdoc
     */
    public function isAdmin(string $email): bool
    {
        $filePath = $this->getConfiguration()->getGsuite()->getAdminByFilePath();

        if (!empty($filePath)) {
            $content = $this->getFileContent($filePath);
            $data = explode("\n", $content);

            return is_array($data) && in_array($email, $data);
        }

        return false;
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

    /**
     * @param string $path
     * @return bool
     */
    protected function isUrl(string $path): bool
    {
        return substr($path, 0, 7) === 'http://' || substr($path, 0, 8) === 'https://';
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getFileContent(string $filePath): string
    {
        $content = '';

        if ($this->isUrl($filePath)) {
            $content = GeneralUtility::getUrl($filePath);
        } else {
            if (substr($filePath, 0, 4) === 'EXT:') {
                $filePath = GeneralUtility::getFileAbsFileName($filePath);
            }
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
            }
        }

        return (string)$content;
    }
}
