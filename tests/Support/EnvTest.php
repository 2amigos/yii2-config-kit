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

use Da\Config\Support\ApplicationConfiguration;
use Da\Config\Support\Env;
use Da\Config\Support\Filesystem;
use Dotenv\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    /**
     * @var Env
     */
    protected $env;

    protected function setUp()
    {
        parent::setUp();

        $envPath = __DIR__ . '/../fixtures';
        $appConfig = new ApplicationConfiguration();
        $appConfig->useRootPath($envPath);
        $this->env = new Env($appConfig, new Filesystem());
    }

    /**
     * Important: Keep this test first!
     */
    public function testValidationException()
    {
        $this->expectException(ValidationException::class);
        $this->env->loadEnvironmentFrom('bad.env')->load();
    }

    public function testEnvironmentFileValues()
    {
        $env = $this->env;
        $env->loadEnvironmentFrom('good.env')->load();

        $this->assertEquals('1', $env->get('YII_DEBUG'));
        $this->assertEquals('dev', $env->get('YII_ENV'));
        $this->assertEquals('my-app', $env->get('APP_NAME'));
        $this->assertEquals('admin@myapp.local', $env->get('APP_ADMIN_EMAIL'));
        $this->assertEquals('mysql:host=localhost;port=3306', $env->get('DATABASE_DSN_BASE'));
        $this->assertEquals('mysql:host=localhost;port=3306;dbname=yii2_app', $env->get('DATABASE_DSN'));
        $this->assertEquals('yii2_app', $env->get('DATABASE_DSN_DB'));
        $this->assertEquals('bar', $env->get('FOO'));
        $this->assertEquals('baz', $env->get('BAR'));
        $this->assertEquals('Hello', $env->get('NVAR1'));
        $this->assertEquals('Hello World!', $env->get('NVAR3'));
        $this->assertEquals('This is default', $env->get('UNKWOWN', 'This is default'));
    }

    public function testChangeEnvironmentFileValues()
    {
        $env = $this->env;
        $fs = new Filesystem();
        $envPath = __DIR__ . '/../fixtures';
        $fs->put($envPath . '/test.env', 'FOO=foo');

        $env
            ->loadEnvironmentFrom('test.env')
            ->changeEnvironmentFile(['FOO' => 'bar']);

        $this->assertStringEqualsFile($envPath . '/test.env', 'FOO=bar');

        $fs->delete($envPath . '/test.env');
    }

    public function testValuesAfterOverload()
    {
        putenv('YII_ENV=prod');

        $this->assertSame('prod', getenv('YII_ENV'));

        $this->env->loadEnvironmentFrom('good.env')->overload();

        $this->assertSame('dev', getenv('YII_ENV'));
        $this->assertSame('dev', $this->env->get('YII_ENV'));
    }
}
