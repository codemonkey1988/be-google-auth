<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var GsuiteConfiguration
     */
    protected $gsuite;

    public function __construct(array $configuration)
    {
        $this->clientId = (string)$configuration['clientId'] ?? '';
        $this->gsuite = GeneralUtility::makeInstance(GsuiteConfiguration::class, (array)$configuration['gsuite.'] ?? []);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return GsuiteConfiguration
     */
    public function getGsuite(): GsuiteConfiguration
    {
        return $this->gsuite;
    }
}
