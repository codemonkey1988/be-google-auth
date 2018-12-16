<?php
declare(strict_types=1);
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
        $subject = new GoogleAuthenticationService();

        $this->assertSame(100, $subject->authUser([]));
    }

    /**
     * @test
     */
    public function authUserWithValidGoogleResponse()
    {
        $email = 'test@example.com';
        $clientMock = $this->getAccessibleMock(Client::class, ['fetchUserProfile']);
        $clientMock
            ->expects($this->once())
            ->method('fetchUserProfile')
            ->willReturn(['email' => $email]);
        $backendUserAuthentication = new BackendUserAuthentication();

        $subject = $this->getAccessibleMock(GoogleAuthenticationService::class, ['getGoogleClient', 'getToken']);
        $subject
            ->expects($this->once())
            ->method('getGoogleClient')
            ->willReturn($clientMock);
        $subject
            ->expects($this->once())
            ->method('getToken')
            ->willReturn('12345');
        $subject->initAuth('test', [], [], $backendUserAuthentication);

        $this->assertSame(200, $subject->authUser(['email' => $email]));
    }
}
