<?php

declare(strict_types=1);

/*
 * This file is part of the "be_google_auth" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Codemonkey1988\BeGoogleAuth\Tests\Unit\Domain\Model\Dto;

use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\ExtensionConfiguration;
use Codemonkey1988\BeGoogleAuth\Domain\Model\Dto\GsuiteConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ExtensionConfigurationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initializeWithEmptyConfiguration()
    {
        $subject = new ExtensionConfiguration([]);

        self::assertSame('', $subject->getClientId());
        self::assertTrue($subject->isLog());
        self::assertInstanceOf(GsuiteConfiguration::class, $subject->getGsuite());
    }

    /**
     * @test
     */
    public function initializeWithClientIdConfiguration()
    {
        $subject = new ExtensionConfiguration([
            'clientId' => '12345',
            'log' => '0',
        ]);

        self::assertSame('12345', $subject->getClientId());
        self::assertFalse($subject->isLog());
    }
}
