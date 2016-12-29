<?php
namespace SideKit\Config\Contracts;

interface ConfigurationBuilderInterface
{
    /**
     * Sets the directory where the configuration files are.
     *
     * @param string $path
     *
     * @return static
     */
    public function useDirectory($path);

    /**
     * Sets the cache file to store resulting config. If null, no configuration will be cached.
     *
     * @param $path
     *
     * @return static
     */
    public function useCacheDirectory($path);

    /**
     * Sets whether to cache the resulting configuration file or not.
     *
     * @param boolean $value
     *
     * @return static
     */
    public function useCache($value);

    /**
     * Builds the configuration file for the application.
     *
     * @param string $name the environment name to build the configuration.
     *
     * @return array
     */
    public function build($name);
}
