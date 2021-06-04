<?php

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Functional\Service;

use Codemonkey1988\BeGoogleAuth\Service\ExtendedAuthenticationService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class ExtendedAuthenticationServiceTest extends FunctionalTestCase
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

    public function setUp()
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function fetchUserRecordNormalAccount()
    {
        $subject = $this->buildAuthenticationService();
        $user = $subject->fetchUserRecord('admin_no_google_oauth@example.com');

        self::assertTrue(is_array($user));
        self::assertSame(30, $user['uid']);
    }

    /**
     * @test
     * @throws \Nimut\TestingFramework\Exception\Exception
     */
    public function fetchUserRecordGoogleAccount()
    {
        $subject = $this->buildAuthenticationService();
        $user = $subject->fetchUserRecord('admin@example.com');

        self::assertFalse($user);
    }

    /**
     * @return ExtendedAuthenticationService
     */
    protected function buildAuthenticationService(): ExtendedAuthenticationService
    {
        $backendUserAuthentication = new BackendUserAuthentication();
        $authenticationService = new ExtendedAuthenticationService();
        $authenticationService->initAuth(
            '',
            [],
            [
                'db_user' => [
                    'table' => $this->table,
                    'check_pid_clause' => '',
                    'enable_clause' => '',
                    'username_column' => 'username',
                ],
            ],
            $backendUserAuthentication
        );

        return $authenticationService;
    }
}
