<?php
namespace SideKit\Config\Support;

use SideKit\Config\Contracts\ConfigurationBuilderInterface;
use SideKit\Config\Contracts\ConfigurationInterface;

/**
 * Class Configuration
 *
 * This class contains all the required folder paths information and by using the configuration builder used for a
 * particular project setup.
 */
class Configuration implements ConfigurationInterface
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
    public function useConfigurationBuilder(ConfigurationBuilderInterface $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function useRootPath($path)
    {
        $this->rootPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @inheritdoc
     */
    public function useBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBasePath()
    {
        return $this->basePath ?: $this->getRootPath() . DIRECTORY_SEPARATOR . 'app';
    }

    /**
     * @inheritdoc
     */
    public function useRuntimePath($path)
    {
        $this->runtimePath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRuntimePath()
    {
        return $this->runtimePath ?: $this->getRootPath() . DIRECTORY_SEPARATOR . 'runtime';
    }

    /**
     * @inheritdoc
     */
    public function useVendorPath($path)
    {
        $this->vendorPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVendorPath()
    {
        return $this->vendorPath ?: $this->getRootPath() . DIRECTORY_SEPARATOR . 'vendor';
    }

    /**
     * @inheritdoc
     */
    public function useConfigPath($path)
    {
        $this->configPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfigPath()
    {
        return $this->configPath ?: $this->getRootPath() . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * @inheritdoc
     */
    public function useEnvConfigPath($path)
    {
        $this->envConfigPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEnvConfigPath()
    {
        return $this->envConfigPath ?: $this->getConfigPath() . DIRECTORY_SEPARATOR . 'env';
    }

    /**
     * @inheritdoc
     */
    public function build($name, $cache = false)
    {
        if ($cache && isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $this->cache[$name] = $this->builder
            ->useDirectory($this->getConfigPath() . DIRECTORY_SEPARATOR . $name)
            ->useCache($cache)
            ->useCacheDirectory($this->getRuntimePath() . DIRECTORY_SEPARATOR . 'config')
            ->build($name);

        return $this->cache[$name];
    }
}
