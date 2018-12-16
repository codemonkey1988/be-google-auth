<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var array
     */
    protected $beUserGroupUids;

    public function __construct(array $configuration)
    {
        $this->enabled = (bool)$configuration['enable'] ?? false;
        $this->organisations = $configuration['organisations'] ? GeneralUtility::trimExplode(',', (string)$configuration['organisations']) : [];
        $this->adminByDefault = (bool)$configuration['adminByDefault'] ?? false;
        $this->beUserGroupUids = $configuration['beUserGroupUids'] ? GeneralUtility::intExplode(',', (string)$configuration['beUserGroupUids']) : [];
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
}
