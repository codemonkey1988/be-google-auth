<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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

        self::assertFalse($subject->isEnabled());
        self::assertFalse($subject->isAdminByDefault());
        self::assertEmpty($subject->getOrganisations());
        self::assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithEnabledConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'enable' => true,
        ]);

        self::assertTrue($subject->isEnabled());
        self::assertFalse($subject->isAdminByDefault());
        self::assertEmpty($subject->getOrganisations());
        self::assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithAdminByDefaultConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'adminByDefault' => true,
        ]);

        self::assertFalse($subject->isEnabled());
        self::assertTrue($subject->isAdminByDefault());
        self::assertEmpty($subject->getOrganisations());
        self::assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithOrganisationsConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'organisations' => 'test.de, example.com ',
        ]);

        self::assertFalse($subject->isEnabled());
        self::assertFalse($subject->isAdminByDefault());
        self::assertEquals(['test.de', 'example.com'], $subject->getOrganisations());
        self::assertEmpty($subject->getBeUserGroupUids());
    }

    /**
     * @test
     */
    public function initializeWithBeUserGroupsConfiguration()
    {
        $subject = new GsuiteConfiguration([
            'beUserGroupUids' => '1, 2,10 ',
        ]);

        self::assertFalse($subject->isEnabled());
        self::assertFalse($subject->isAdminByDefault());
        self::assertEmpty($subject->getOrganisations());
        self::assertEquals(['1', '2', '10'], $subject->getBeUserGroupUids());
    }
}
