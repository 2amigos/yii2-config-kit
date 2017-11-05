<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Support;

use Da\Config\Contracts\ApplicationConfigurationInterface;
use Da\Config\Contracts\ConfigurationBuilderInterface;

/**
 * Class Configuration
 *
 * This class contains all the required folder paths information and by using the configuration builder used for a
 * particular project setup.
 */
class ApplicationConfiguration implements ApplicationConfigurationInterface
{
    /**
     * @var string application top most folder
     */
    protected $rootPath;
    /**
     * @var string application base path
     */
    protected $basePath;
    /**
     * @var string runtime path
     */
    protected $runtimePath;
    /**
     * @var string Composer's vendor path
     */
    protected $vendorPath;
    /**
     * @var string application config folder path
     */
    protected $configPath;
    /**
     * @var string environment specific config folder path
     */
    protected $envConfigPath;
    /**
     * @var array caches the configuration so its accessible at all times.
     */
    protected $cache;
    /**
     * @var ConfigurationBuilderInterface
     */
    protected $builder;

    /**
     * Configuration constructor.
     *
     * @param ConfigurationBuilderInterface $builder
     */
    public function __construct(ConfigurationBuilderInterface $builder = null)
    {
        if ($builder !== null) {
            $this->useConfigurationBuilder($builder);
        }
    }

    /**
     * @inheritdoc
     */
    public function useConfigurationBuilder(ConfigurationBuilderInterface $builder): ApplicationConfigurationInterface
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function useRootPath(string $path): ApplicationConfigurationInterface
    {
        $this->rootPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @inheritdoc
     */
    public function useBasePath(string $path): ApplicationConfigurationInterface
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBasePath(): string
    {
        return $this->basePath ?: $this->getRootPath() . '/app';
    }

    /**
     * @inheritdoc
     */
    public function useRuntimePath(string $path): ApplicationConfigurationInterface
    {
        $this->runtimePath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRuntimePath(): string
    {
        return $this->runtimePath ?: $this->getRootPath() . '/runtime';
    }

    /**
     * @inheritdoc
     */
    public function useVendorPath(string $path): ApplicationConfigurationInterface
    {
        $this->vendorPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVendorPath(): string
    {
        return $this->vendorPath ?: $this->getRootPath() . '/vendor';
    }

    /**
     * @inheritdoc
     */
    public function useConfigPath(string $path): ApplicationConfigurationInterface
    {
        $this->configPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfigPath(): string
    {
        return $this->configPath ?: $this->getRootPath() . '/config';
    }

    /**
     * @inheritdoc
     */
    public function useEnvConfigPath(string $path): ApplicationConfigurationInterface
    {
        $this->envConfigPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEnvConfigPath(): string
    {
        return $this->envConfigPath ?: $this->getConfigPath() . '/env';
    }

    /**
     * @inheritdoc
     */
    public function build(string $name, bool $cache = false): array
    {
        if ($cache && isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $this->cache[$name] = $this->builder
            ->useDirectory($this->getConfigPath() . DIRECTORY_SEPARATOR . $name)
            ->useCache($cache)
            ->useCacheDirectory($this->getRuntimePath() . '/config')
            ->build($name);

        return $this->cache[$name];
    }
}
