<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Service;

use Codemonkey1988\BeGoogleAuth\Google\Client;
use Codemonkey1988\BeGoogleAuth\Service\GoogleAuthenticationService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class GoogleAuthenticationServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function authUserWithInvalidGoogleResponse()
    {
        $subject = $this->getAccessibleMock(GoogleAuthenticationService::class, ['log']);

        self::assertSame(100, $subject->authUser([]));
    }

    /**
     * @test
     */
    public function authUserWithValidGoogleResponse()
    {
        $email = 'test@example.com';
        $clientMock = $this->getAccessibleMock(Client::class, ['fetchUserProfile']);
        $clientMock
            ->expects(self::once())
            ->method('fetchUserProfile')
            ->willReturn(['email' => $email]);
        $backendUserAuthentication = new BackendUserAuthentication();

        $subject = $this->getAccessibleMock(GoogleAuthenticationService::class, ['getGoogleClient', 'getToken', 'log']);
        $subject
            ->expects(self::once())
            ->method('getGoogleClient')
            ->willReturn($clientMock);
        $subject
            ->expects(self::once())
            ->method('getToken')
            ->willReturn('12345');
        $subject->initAuth('test', [], [], $backendUserAuthentication);

        self::assertSame(200, $subject->authUser(['email' => $email]));
    }
}
