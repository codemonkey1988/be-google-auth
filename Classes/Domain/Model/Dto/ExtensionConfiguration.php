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
 * Class ExtensionConfiguration
 */
class ExtensionConfiguration
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var bool
     */
    protected $log;

    /**
     * @var GsuiteConfiguration
     */
    protected $gsuite;

    public function __construct(array $configuration)
    {
        $this->clientId = (string)$configuration['clientId'] ?? '';
        $this->log = isset($configuration['log']) ? !empty($configuration['log']) : true;
        $this->gsuite = GeneralUtility::makeInstance(GsuiteConfiguration::class, (array)$configuration['gsuite'] ?? []);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return bool
     */
    public function isLog(): bool
    {
        return $this->log;
    }

    /**
     * @return GsuiteConfiguration
     */
    public function getGsuite(): GsuiteConfiguration
    {
        return $this->gsuite;
    }
}
