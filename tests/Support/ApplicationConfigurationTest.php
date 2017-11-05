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

use Da\Config\Contracts\ApplicationConfigurationInterface;
use Da\Config\Contracts\ConfigurationBuilderInterface;
use Da\Config\Support\ApplicationConfiguration;
use Da\Config\Support\Filesystem;
use PHPUnit\Framework\TestCase;

class ApplicationConfigurationTest extends TestCase
{
    /**
     * @var ConfigurationBuilderInterface
     */
    protected $builder;
    /**
     * @var ApplicationConfigurationInterface
     */
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ConfigurationBuilder();
        $this->app = new ApplicationConfiguration($this->builder);
        $this->app
            ->useRootPath(dirname(__DIR__) . '/fixtures');
    }

    public function testAllPossibleConfigurationValuesAndBuilderResults()
    {
        $base = dirname(__DIR__);

        $this->assertEquals($base . '/fixtures', $this->app->getRootPath());
        $this->assertEquals($base . '/fixtures/config', $this->app->getConfigPath());
        $this->assertEquals($base . '/fixtures/runtime', $this->app->getRuntimePath());
        $this->assertEquals($base . '/fixtures/vendor', $this->app->getVendorPath());
        $this->assertEquals($base . '/fixtures/app', $this->app->getBasePath());

        $config = $this->app->build('dev');

        $this->assertEquals('dev', $config['env']);

        $config = $this->app->build('prod');

        $this->assertEquals('prod', $config['env']);

        $class = get_class($this->app);
        $this->assertInstanceOf($class, $this->app->useRootPath($base));
        $this->assertEquals($base, $this->app->getRootPath());
        $this->assertInstanceOf($class, $this->app->useConfigPath($base . '/config'));
        $this->assertEquals($base . '/config', $this->app->getConfigPath());
        $this->assertInstanceOf($class, $this->app->useRuntimePath($base . '/runtime'));
        $this->assertEquals($base . '/runtime', $this->app->getRuntimePath());
        $this->assertInstanceOf($class, $this->app->useVendorPath($base . '/vendor'));
        $this->assertEquals($base . '/vendor', $this->app->getVendorPath());
        $this->assertInstanceOf($class, $this->app->useBasePath($base . '/base'));
        $this->assertEquals($base . '/base', $this->app->getBasePath());
    }
}

class ConfigurationBuilder implements ConfigurationBuilderInterface
{
    private $directory;
    private $cacheDir;
    private $cache;

    public function useDirectory(string $path): ConfigurationBuilderInterface
    {
        $this->directory = $path;

        return $this;
    }

    public function useCacheDirectory(string $path): ConfigurationBuilderInterface
    {
        $this->cacheDir = $path;

        return $this;
    }

    public function useCache(bool $value): ConfigurationBuilderInterface
    {
        $this->cache = $value;

        return $this;
    }

    public function build(string $name): array
    {
        $fs = new Filesystem();

        $cached = $this->cacheDir . '/' . $name . '-config.php';

        if ($this->cache && $fs->exists($cached)) {
            return $fs->getRequiredFileValue($cached);
        }

        $config = $fs->getRequiredFileValue($this->directory . '/config.php');

        if ($this->cache) {
            $varStr = str_replace('\\\\', '\\', var_export($config, true));
            $fs->put($cached, "<?php\n\nreturn {$varStr};");
        }

        return $config;
    }
}
