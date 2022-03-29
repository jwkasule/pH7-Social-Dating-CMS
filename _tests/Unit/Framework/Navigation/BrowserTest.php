<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2020-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Browser;
use PHPUnit\Framework\TestCase;

final class BrowserTest extends TestCase
{
    private Browser $oBrowser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oBrowser = new Browser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupGlobalServerVars();
    }

    /**
     * @dataProvider defaultBrowserHexCodesProvider
     */
    public function testFoundDefaultBrowserHexCode(string $sHexCode): void
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound($sHexCode);

        $this->assertTrue($bResult);
    }

    public function testNotFoundDefaultBrowserHexCode(): void
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound('#FFF');

        $this->assertFalse($bResult);
    }

    public function testIfModifiedSinceHeaderExists(): void
    {
        $sExpectedDate = 'Tue, 29 Feb 2022 08:16:20 GMT';

        $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $sExpectedDate;

        $this->assertSame($sExpectedDate, $this->oBrowser->getIfModifiedSince());
    }

    public function testIfModifiedSinceHeaderDoesNotExist(): void
    {
        unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);

        $this->assertNull($this->oBrowser->getIfModifiedSince());
    }

    /**
     * @dataProvider encodingServerHeadersProvider
     */
    public function testGetEncodingType(string $sEncodingType): void
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = $sEncodingType;

        $this->assertSame($sEncodingType, $this->oBrowser->encoding());
    }

    public function testInvalidEncoding(): void
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'wrong encoding type';

        $this->assertFalse($this->oBrowser->encoding());
    }

    /**
     * @dataProvider mobileServerHeadersProvider
     */
    public function testIsMobile(string $sServerKeyName, string $sServerValue): void
    {
        $_SERVER[$sServerKeyName] = $sServerValue;

        $this->assertTrue($this->oBrowser->isMobile());
    }

    public function testIsNotMobile(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Windows ...';

        $this->assertFalse($this->oBrowser->isMobile());
    }

    public function defaultBrowserHexCodesProvider(): array
    {
        return [
            ['#000'],
            ['#000000']
        ];
    }

    public function encodingServerHeadersProvider(): array
    {
        return [
            ['gzip'],
            ['x-gzip'],
        ];
    }

    public function mobileServerHeadersProvider(): array
    {
        return [
            ['HTTP_X_WAP_PROFILE', 'something'],
            ['HTTP_PROFILE', 'something'],
            ['HTTP_USER_AGENT', 'Mobile'],
            ['HTTP_USER_AGENT', 'Phone'],
            ['HTTP_USER_AGENT', 'Android 123'],
            ['HTTP_USER_AGENT', 'My Opera Mini 000'],
        ];
    }

    private function cleanupGlobalServerVars(): void
    {
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);
        unset($_SERVER['HTTP_X_WAP_PROFILE']);
        unset($_SERVER['HTTP_PROFILE']);
        unset($_SERVER['HTTP_USER_AGENT']);
        unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    }
}
