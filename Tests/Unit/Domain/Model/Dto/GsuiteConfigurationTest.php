<?php
declare(strict_types=1);
namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\Domain\Model\Dto;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\GsuiteConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class GsuiteConfigurationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initializeWithEmptyConfiguration()
    {
        $subject = new GsuiteConfiguration([]);

        $this->assertFalse($subject->isEnabled());
        $this->assertFalse($subject->isAdminByDefault());
        $this->assertEmpty($subject->getOrganisations());
        $this->assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithEnabledConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'enable' => true,
        ]);

        $this->assertTrue($subject->isEnabled());
        $this->assertFalse($subject->isAdminByDefault());
        $this->assertEmpty($subject->getOrganisations());
        $this->assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithAdminByDefaultConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'adminByDefault' => true,
        ]);

        $this->assertFalse($subject->isEnabled());
        $this->assertTrue($subject->isAdminByDefault());
        $this->assertEmpty($subject->getOrganisations());
        $this->assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithOrganisationsConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'organisations' => 'test.de, example.com ',
        ]);

        $this->assertFalse($subject->isEnabled());
        $this->assertFalse($subject->isAdminByDefault());
        $this->assertEquals(['test.de', 'example.com'], $subject->getOrganisations());
        $this->assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithBeUserGroupsConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'beUserGroupUids' => '1, 2,10 ',
        ]);

        $this->assertFalse($subject->isEnabled());
        $this->assertFalse($subject->isAdminByDefault());
        $this->assertEmpty($subject->getOrganisations());
        $this->assertEquals(['1', '2', '10'], $subject->getBeUserGroupUids());
    }
}
