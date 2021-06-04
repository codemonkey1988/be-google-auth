<?php

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Functional\Service;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Google\Client;
use Codemonkey1988\BeGoogleAuth\Service\ConfigurationService;
use Codemonkey1988\BeGoogleAuth\Service\GoogleAuthenticationService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GoogleAuthenticationServiceTest extends FunctionalTestCase
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

    public function setUp()
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');
    }

    /**
     * @test
     */
    public function getExistingAdminUser()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'admin@example.com',
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertSame(1, $user['uid']);
    }

    /**
     * @test
     */
    public function getNonExistingAdminUserWithDisabledGsuiteFeature()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'admin1@example.com',
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertEmpty($user);
    }

    /**
     * @test
     */
    public function getNonExistingAdminUserWithEnabledGsuiteFeatureAndMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'admin1@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'adminByDefault' => true,
                    'organisations' => 'example.com',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertSame('admin1@example.com', $user['email']);
        self::assertSame(1, $user['admin']);
    }

    /**
     * @test
     */
    public function getNonExistingAdminUserWithEnabledGsuiteFeatureAndNonMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'admin1@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'adminByDefault' => true,
                    'organisations' => 'example.org',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertEmpty($user);
    }

    /**
     * @test
     */
    public function getNonExistingEditorUserWithEnabledGsuiteFeatureAndMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'user1@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'beUserGroupUids' => '1,2',
                    'organisations' => 'example.com',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertSame('user1@example.com', $user['email']);
        self::assertSame(0, $user['admin']);
        self::assertSame('1,2', $user['usergroup']);
    }

    /**
     * @test
     */
    public function getNonExistingEditorUserWithEnabledGsuiteFeatureAndNonMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'user1@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'beUserGroupUids' => '1,2',
                    'organisations' => 'example.org',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertEmpty($user);
    }

    /**
     * @test
     */
    public function getDeletedEditorUserWithEnabledGsuiteFeatureAndMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'editor_deleted@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'organisations' => 'example.com',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        self::assertSame(20, $user['uid']);
        self::assertSame(0, $user['admin']);
        self::assertSame(0, $user['deleted']);
    }

    /**
     * @test
     */
    public function getDisabledEditorUserWithEnabledGsuiteFeatureAndMatchingOrganisation()
    {
        $subject = $this->buildAuthenticationServiceMock(
            [
                'email' => 'editor_disabled@example.com',
                'hd' => 'example.com',
            ],
            [
                'gsuite' => [
                    'enable' => true,
                    'organisations' => 'example.com',
                ],
            ]
        );
        $user = $subject->getUser();

        self::assertTrue(is_array($user));
        $this->assertempty($user);
    }

    /**
     * @param array $googleResponse
     * @param array $configuration
     * @return \Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function buildAuthenticationServiceMock(array $googleResponse, array $configuration = [])
    {
        if (version_compare(TYPO3_version, '9.5.0', '<')) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_google_auth'] = serialize($configuration);
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_google_auth'] = $configuration;
        }
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $restrictionContainer = GeneralUtility::makeInstance(DefaultRestrictionContainer::class);
        $restrictionContainer->add(GeneralUtility::makeInstance(RootLevelRestriction::class, [$this->table]));
        $backendUserAuthentication = new BackendUserAuthentication();

        $googleClientMock = $this->getAccessibleMock(Client::class, ['fetchUserProfile']);
        $googleClientMock
            ->expects(self::once())
            ->method('fetchUserProfile')
            ->willReturn($googleResponse);

        $configurationServiceMock = $this->getAccessibleMock(ConfigurationService::class, ['getConfiguration']);
        $configurationServiceMock
            ->method('getConfiguration')
            ->willReturn(new ExtensionConfiguration($configuration));

        $authenticationService = $this->getAccessibleMock(GoogleAuthenticationService::class, ['getGoogleClient', 'getToken']);
        $authenticationService
            ->expects(self::once())
            ->method('getGoogleClient')
            ->willReturn($googleClientMock);
        $authenticationService
            ->expects(self::once())
            ->method('getToken')
            ->willReturn('12345');

        $authenticationService->initAuth(
            '',
            [
                'status' => 'login',
            ],
            [
                'loginType' => 'BE',
                'db_user' => [
                    'table' => $this->table,
                    'check_pid_clause' => '',
                    'enable_clause' => $restrictionContainer->buildExpression(
                        [
                            $this->table => $this->table,
                        ],
                        $queryBuilder->expr()
                    ),
                    'username_column' => 'username',
                ],
            ],
            $backendUserAuthentication
        );

        return $authenticationService;
    }
}
