<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Functional\UserProvider;

use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use Codemonkey1988\BeGoogleAuth\UserProvider\BackendUserProvider;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserProviderTest extends FunctionalTestCase
{
    /**
     * @var string
     */
    protected $table = 'be_users';

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_google_auth',
    ];

    /**
     * Force bcrypt hashing algorithm because travis ci php 7.2 does not support argon2
     * which is default in TYPO3 v10.
     *
     * @var array
     */
    protected $configurationToUseInTestInstance = [
        'BE' => [
            'passwordHashing' => [
                'className' => \TYPO3\CMS\Core\Crypto\PasswordHashing\BcryptPasswordHash::class,
            ],
        ],
    ];

    /**
     * @test
     */
    public function createNewAdminUser()
    {
        $configurationService = $this->getExtensionService([
            'gsuite' => [
                'adminByDefault' => '1',
            ],
        ]);

        $userProvider = new BackendUserProvider([]);
        $userProvider->injectConfigurationService($configurationService);
        $userProvider->createUser('test@example.com', 'Test user');

        $queryBuilder = $this->getQueryBuilderForTable($this->table);
        $users = $queryBuilder
            ->select('*')
            ->from($this->table)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter('test@example.com'))
            )
            ->execute()
            ->fetchAll();

        self::assertCount(1, $users);
        self::assertSame(1, $users[0]['admin']);
        self::assertSame('Test user', $users[0]['realName']);
        self::assertNotEmpty($users[0]['password']);
        self::assertEmpty($users[0]['usergroup']);
    }

    /**
     * @test
     */
    public function createNewNonAdminUser()
    {
        $configurationService = $this->getExtensionService([
            'gsuite' => [
                'adminByDefault' => '0',
                'beUserGroupUids' => '1,2',
            ],
        ]);

        $userProvider = new BackendUserProvider([]);
        $userProvider->injectConfigurationService($configurationService);
        $userProvider->createUser('test@example.com', 'Test user');

        $queryBuilder = $this->getQueryBuilderForTable($this->table);
        $users = $queryBuilder
            ->select('*')
            ->from($this->table)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter('test@example.com'))
            )
            ->execute()
            ->fetchAll();

        self::assertCount(1, $users);
        self::assertSame(0, $users[0]['admin']);
        self::assertSame('Test user', $users[0]['realName']);
        self::assertNotEmpty($users[0]['password']);
        self::assertSame('1,2', $users[0]['usergroup']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getActiveAdminUser()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('admin@example.com');

        self::assertNotEmpty($user);
        self::assertSame(1, $user['uid']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getActiveNonAdminUser()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('editor@example.com');

        self::assertNotEmpty($user);
        self::assertSame(2, $user['uid']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getDeletedAdminUser()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('admin_deleted@example.com');

        self::assertEmpty($user);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getDisabledAdminUser()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('admin_disabled@example.com');

        self::assertEmpty($user);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getDeletedAdminUserWithoutRestrictions()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('admin_deleted@example.com', false);

        self::assertNotEmpty($user);
        self::assertSame(10, $user['uid']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function getDisabledAdminUserWithoutRestrictions()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);

        $user = $userProvider->getUserByEmail('admin_disabled@example.com', false);

        self::assertNotEmpty($user);
        self::assertSame(11, $user['uid']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function restoreDeletedAdminUser()
    {
        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');

        $configurationService = $this->getExtensionService([]);
        $userProvider = new BackendUserProvider($this->buildAuthenticationInformation());
        $userProvider->injectConfigurationService($configurationService);
        $userProvider->restoreUser(['uid' => 10, 'username' => 'admin_deleted@example.com']);

        $user = $userProvider->getUserByEmail('admin_deleted@example.com');

        self::assertNotEmpty($user);
        self::assertSame(10, $user['uid']);
    }

    /**
     * @param array $configuration
     * @return ConfigurationService
     */
    protected function getExtensionService(array $configuration)
    {
        if (version_compare(TYPO3_version, '9.5.0', '<')) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_google_auth'] = serialize($configuration);
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_google_auth'] = $configuration;
        }

        return new ConfigurationService();
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    protected function getQueryBuilderForTable(string $table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

    /**
     * @return array
     */
    protected function buildAuthenticationInformation(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);

        $restrictionContainer = GeneralUtility::makeInstance(DefaultRestrictionContainer::class);
        $restrictionContainer->add(GeneralUtility::makeInstance(RootLevelRestriction::class, [$this->table]));

        return [
            'db_user' => [
                'table' => $this->table,
                'enable_clause' => $restrictionContainer->buildExpression(
                    [
                        $this->table => $this->table,
                    ],
                    $queryBuilder->expr()
                ),
            ],
        ];
    }
}
