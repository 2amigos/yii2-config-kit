<?php
namespace SideKit\Config\Contracts;

interface ConfigurationInterface
{
    /**
     * Sets the configuration builder for the project
     *
     * @param ConfigurationBuilderInterface $builder
     *
     * @return ConfigurationInterface
     */
    public function useConfigurationBuilder(ConfigurationBuilderInterface $builder);

    /**
     * Configures the root directory of the application.
     *
     * @param string $path
     *
     * @return ConfigurationInterface
     */
    public function useRootPath($path);

    /**
     * Configures the root directory of the application.
     *
     * @param string $path
     *
     * @return ConfigurationInterface
     */
    public function useBasePath($path);

    /**
     * Sets the directory to be used as runtime path.
     *
     * @param string $path
     *
     * @return ConfigurationInterface
     */
    public function useRuntimePath($path);

    /**
     * Sets composer's vendor directory.
     *
     * @param $path
     *
     * @return ConfigurationInterface
     */
    public function useVendorPath($path);

    /**
     * Sets the configuration folder path.
     *
     * @param string $path
     *
     * @return ConfigurationInterface
     */
    public function useConfigPath($path);

    /**
     * Sets the environments specific configuration folder path.
     *
     * @param string $path
     *
     * @return ConfigurationInterface
     */
    public function useEnvConfigPath($path);

    /**
     * @return string the top most root folder path.
     */
    public function getRootPath();

    /**
     * @return string the application's base folder path
     */
    public function getBasePath();

    /**
     * @return string the runtime folder path.
     */
    public function getRuntimePath();

    /**
     * @return string the composer's vendor folder path.
     */
    public function getVendorPath();

    /**
     * @return string the configuration folder path.
     */
    public function getConfigPath();

    /**
     * @return string the environments specific configuration folder.
     */
    public function getEnvConfigPath();

    /**
     * Builds and returns the configuration based on the paths provided.
     *
     * @param string $name the configuration name to load. The name of the configuration must be a sub-folder name
     * under the configuration directory
     * @param bool $cache whether to cache results or not
     *
     * @return array
     */
    public function build($name, $cache = false);
}
