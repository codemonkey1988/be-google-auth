<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\UserProvider\Permission;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\AdminByFileBackendUserPermission;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AdminByFileBackendUserPermissionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function isNoAdminByDefault()
    {
        $subject = new AdminByFileBackendUserPermission();

        $this->assertFalse($subject->isAdmin(''));
    }

    /**
     * @test
     */
    public function isNoAdminWhenConfigured()
    {
        $subject = $this->buildSimpleBackendUserPermissionMock([
            'gsuite.' => [
                'adminByFilePath' => 'EXT:be_google_auth/Tests/Unit/Fixtures/adminEmails.txt',
            ],
        ]);

        $this->assertTrue($subject->isAdmin('me@test.com'));
    }

    /**
     * @test
     */
    public function emptyUserGroupUidsByDefault()
    {
        $subject = new AdminByFileBackendUserPermission();

        $this->assertSame([], $subject->getUserGroupUids(''));
    }

    /**
     * @test
     */
    public function correctUserGroupUidsCWhenConfigured()
    {
        $subject = $this->buildSimpleBackendUserPermissionMock([
            'gsuite.' => [
                'beUserGroupUids' => '1,2',
            ],
        ]);

        $this->assertSame([1, 2], $subject->getUserGroupUids(''));
    }

    /**
     * @param array $configuration
     * @return \Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function buildSimpleBackendUserPermissionMock(array $configuration = [])
    {
        $subject = $this->getAccessibleMock(AdminByFileBackendUserPermission::class, ['getConfiguration']);
        $subject
            ->method('getConfiguration')
            ->willReturn(new ExtensionConfiguration($configuration));

        return $subject;
    }
}
