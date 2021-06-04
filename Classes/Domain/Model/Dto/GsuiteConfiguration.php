<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GsuiteConfiguration
 */
class GsuiteConfiguration
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var array
     */
    protected $organisations;

    /**
     * @var bool
     */
    protected $adminByDefault;

    /**
     * @var string
     */
    protected $adminByFilePath;

    /**
     * @var array
     */
    protected $beUserGroupUids;

    public function __construct(array $configuration)
    {
        $this->enabled = (bool)$configuration['enable'] ?? false;
        $this->organisations = $configuration['organisations'] ? GeneralUtility::trimExplode(',', (string)$configuration['organisations']) : [];
        $this->adminByDefault = (bool)$configuration['adminByDefault'] ?? false;
        $this->beUserGroupUids = $configuration['beUserGroupUids'] ? GeneralUtility::intExplode(',', (string)$configuration['beUserGroupUids']) : [];
        $this->adminByFilePath = (string)$configuration['adminByFilePath'] ?: '';
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getOrganisations(): array
    {
        return $this->organisations;
    }

    /**
     * @return bool
     */
    public function isAdminByDefault(): bool
    {
        return $this->adminByDefault;
    }

    /**
     * @return array
     */
    public function getBeUserGroupUids(): array
    {
        return $this->beUserGroupUids;
    }

    /**
     * @return string
     */
    public function getAdminByFilePath(): string
    {
        return $this->adminByFilePath;
    }
}
