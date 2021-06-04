<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\UserProvider\Permission;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\UserProvider\Permission\SimpleBackendUserPermission;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class SimpleBackendUserPermissionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function isNoAdminByDefault()
    {
        $subject = new SimpleBackendUserPermission();

        self::assertFalse($subject->isAdmin(''));
    }

    /**
     * @test
     */
    public function isNoAdminWhenConfigured()
    {
        $subject = $this->buildSimpleBackendUserPermissionMock([
            'gsuite' => [
                'adminByDefault' => true,
            ],
        ]);

        self::assertTrue($subject->isAdmin(''));
    }

    /**
     * @test
     */
    public function emptyUserGroupUidsByDefault()
    {
        $subject = new SimpleBackendUserPermission();

        self::assertSame([], $subject->getUserGroupUids(''));
    }

    /**
     * @test
     */
    public function correctUserGroupUidsCWhenConfigured()
    {
        $subject = $this->buildSimpleBackendUserPermissionMock([
            'gsuite' => [
                'beUserGroupUids' => '1,2',
            ],
        ]);

        self::assertSame([1, 2], $subject->getUserGroupUids(''));
    }

    /**
     * @param array $configuration
     * @return \Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function buildSimpleBackendUserPermissionMock(array $configuration = [])
    {
        $subject = $this->getAccessibleMock(SimpleBackendUserPermission::class, ['getConfiguration']);
        $subject
            ->method('getConfiguration')
            ->willReturn(new ExtensionConfiguration($configuration));

        return $subject;
    }
}
