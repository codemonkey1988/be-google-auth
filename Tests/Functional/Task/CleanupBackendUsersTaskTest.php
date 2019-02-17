<?php
namespace Codemonkey1988\BeGoogleAuth\Tests\Functional\Task;

use Codemonkey1988\BeGoogleAuth\Task\CleanupBackendUsersTask;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

/**
 * Class CleanupBackendUsersTaskTest
 */
class CleanupBackendUsersTaskTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_google_auth',
    ];

    protected $coreExtensionsToLoad = [
        'scheduler',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../Fixtures/be_users.xml');
    }

    /**
     * @test
     */
    public function successfulDeleteGoogleUser()
    {
        $subject = $this->getAccessibleMock(CleanupBackendUsersTask::class, ['getTimeComparison']);
        $subject
            ->method('getTimeComparison')
            ->willReturn(1550404800);

        $this->assertTrue($subject->execute());
        $this->assertSame(0, $this->getDatabaseConnection()->selectCount('uid', 'be_users', 'uid=1 AND deleted = 0'));
    }

    /**
     * @test
     */
    public function notDeletingUserLastloginToFresh()
    {
        $subject = $this->getAccessibleMock(CleanupBackendUsersTask::class, ['getTimeComparison']);
        $subject
            ->method('getTimeComparison')
            ->willReturn(1550404799);

        $this->assertTrue($subject->execute());
        $this->assertSame(1, $this->getDatabaseConnection()->selectCount('uid', 'be_users', 'uid=1 AND deleted = 0'));
    }

    /**
     * @test
     */
    public function notDeletingUserNoGoogleUser()
    {
        $subject = $this->getAccessibleMock(CleanupBackendUsersTask::class, ['getTimeComparison']);
        $subject
            ->method('getTimeComparison')
            ->willReturn(1550404800);

        $this->assertTrue($subject->execute());
        $this->assertSame(1, $this->getDatabaseConnection()->selectCount('uid', 'be_users', 'uid=30 AND deleted = 0'));
    }
}
