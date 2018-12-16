<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Google;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;

class Gsuite implements SingletonInterface
{
    /**
     * @var ExtensionConfiguration
     */
    protected $configuration;

    /**
     * @param ConfigurationService $configurationService
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configuration = $configurationService->getConfiguration();
    }

    public function enabled(): bool
    {
        return $this->configuration->getGsuite()->isEnabled();
    }

    public function isGsuiteUser(array $userData): bool
    {
        return !empty($userData['hd']);
    }

    public function isInOrganisation(array $userData): bool
    {
        if (empty($userData['hd'])) {
            return false;
        }

        return in_array($userData['hd'], $this->configuration->getGsuite()->getOrganisations());
    }

    public function shouldCreateAdminUser(): bool
    {
        return $this->configuration->getGsuite()->isAdminByDefault();
    }

    public function getUserGroupUids(): array
    {
        return $this->configuration->getGsuite()->getBeUserGroupUids();
    }
}
