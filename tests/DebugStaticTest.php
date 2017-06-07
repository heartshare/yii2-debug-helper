<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\debug\tests;

use PHPUnit\Framework\TestCase;
use sonrac\debug\Debug;
use sonrac\debug\IDebug;

/**
 * Class DebugStaticTest
 * Test DebugStaticTest
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class DebugStaticTest extends TestCase
{
    public function testDebugIp() {
        $this->assertFalse(Debug::checkCookieDeveloper());
        $_COOKIE[Debug::COOKIE_DEVELOPER] = true;
        $this->assertTrue(Debug::checkCookieDeveloper());
        $_COOKIE = [];
        $this->assertFalse(Debug::checkCookieDeveloper());
        $_SERVER['REMOTE_ADDR'] = '32.32.32.32';
        $this->assertTrue(Debug::isDeveloper());

        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $this->assertFalse(Debug::isDeveloper());
    }

    public function testStaticCalls() {
        /**
         * @method static |string  getDir($default = 'origin/', $alternate = 'min/')
         * @method static |array   ips()
         * @method static |bool    enableKint()
         * @method static |string  getKintTheme()

         */
        $this->assertNull(Debug::shortTrace(1));
        $this->assertNull(Debug::dm(1));
        $this->assertNull(Debug::preBegin());
        $this->assertNull(Debug::preEnd());
        $this->assertInternalType('array', Debug::getIps());
        $this->assertFalse(Debug::enableKint());
        $this->assertEquals('none', Debug::getDir('dir', 'none'));
        $this->assertEquals('[date time]: new_line', Debug::getFormat());
        $this->assertEquals(8, Debug::traceLevel());
        $this->assertFalse(Debug::exit());
        $this->assertNull(Debug::pr(1));
        $this->assertNull(Debug::ex(0, false));
        $this->assertEquals(PHP_EOL, Debug::getEOL());
        $this->assertNull(Debug::str('ast'));
        $this->assertFalse(Debug::isDeveloper());
        $this->assertFalse(Debug::isDeveloperMode());
        $this->assertFalse(Debug::checkKint());
        $this->assertNull(Debug::trace());
        $this->assertNull(Debug::trace());
        $this->assertFalse(Debug::checkCookieDeveloper());
        $this->assertNull(Debug::exp(123));
        $this->assertTrue(Debug::getUseFormat());
        $this->assertTrue(Debug::getCheckDeveloperWithIp());
        $this->assertTrue(Debug::getCheckEnvironmentSettings());
        $this->assertEquals('test', Debug::getName('test', ''));
        $this->assertFalse(Debug::checkKint());
        $this->assertTrue(Debug::getCookieCheck());
        $this->assertEquals(IDebug::COOKIE_DEVELOPER, Debug::getCookieName());
        $this->assertFalse(Debug::enableKint());

    }
}
