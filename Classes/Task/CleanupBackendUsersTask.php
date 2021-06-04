<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Task;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class CleanupBackendUsersTask
 */
class CleanupBackendUsersTask extends AbstractTask
{
    public $daysSinceLastLogin;

    /**
     * @throws \Exception
     * @return bool
     */
    public function execute(): bool
    {
        if ($this->daysSinceLastLogin < 0) {
            return false;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder
            ->update('be_users')
            ->where(
                $queryBuilder->expr()->eq('google_oauth', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)),
                $queryBuilder->expr()->lte('lastlogin', $queryBuilder->createNamedParameter($this->getTimeComparison(), \PDO::PARAM_INT))
            )
            ->set('deleted', 1)
            ->execute();

        return true;
    }

    /**
     * @throws \Exception
     * @return int
     */
    protected function getTimeComparison(): int
    {
        return (new \DateTime('-' . $this->daysSinceLastLogin . ' days'))->getTimestamp();
    }
}
