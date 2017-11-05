<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Tests\Support;

use Da\Config\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    /**
     * @var Str
     */
    protected $str;

    protected function setUp()
    {
        parent::setUp();

        $this->str = new Str();
    }

    /**
     * @dataProvider isProvider
     *
     * @param $pattern
     * @param $value
     */
    public function testIs($pattern, $value)
    {
        $this->assertTrue($this->str->is($pattern, $value));
    }

    /**
     * @dataProvider notIsProvider
     *
     * @param $pattern
     * @param $value
     */
    public function testNotIs($pattern, $value)
    {
        $this->assertFalse($this->str->is($pattern, $value));
    }

    /**
     * @dataProvider upperProvider
     *
     * @param $value
     * @param $upper
     */
    public function testUpper($value, $upper)
    {
        $this->assertEquals($this->str::upper($value), $upper);
    }

    /**
     * @dataProvider lowerProvider
     *
     * @param $value
     * @param $lower
     */
    public function testLower($value, $lower)
    {
        $this->assertEquals($this->str::lower($value), $lower);
    }

    public function lowerProvider()
    {
        return [
            ['local', 'local'],
            ['cañada', 'cañada'],
            ['PROduction', 'production'],
            ['StAgINg', 'staging'],
        ];
    }

    public function upperProvider()
    {
        return [
            ['local', 'LOCAL'],
            ['cañada', 'CAÑADA'],
            ['PROduction', 'PRODUCTION'],
            ['StAgINg', 'STAGING'],
        ];
    }

    public function notIsProvider()
    {
        return [
            ['local', 'n'],
            ['prod', 'prod*'],
            ['stage', 'ståge']
        ];
    }

    public function isProvider()
    {
        return [
            ['local', 'local'],
            ['prod*', 'prod'],
            ['*prod', 'prod']
        ];
    }
}
