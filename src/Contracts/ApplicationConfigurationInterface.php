<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Contracts;

interface ApplicationConfigurationInterface
{
    /**
     * Sets the configuration builder for the project
     *
     * @param ConfigurationBuilderInterface $builder
     *
     * @return ApplicationConfigurationInterface
     */
    public function useConfigurationBuilder(ConfigurationBuilderInterface $builder): ApplicationConfigurationInterface;

    /**
     * Configures the root directory of the application.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useRootPath(string $path): ApplicationConfigurationInterface;

    /**
     * Configures the root directory of the application.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useBasePath(string $path): ApplicationConfigurationInterface;

    /**
     * Sets the directory to be used as runtime path.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useRuntimePath(string $path): ApplicationConfigurationInterface;

    /**
     * Sets composer's vendor directory.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useVendorPath(string $path): ApplicationConfigurationInterface;

    /**
     * Sets the configuration folder path.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useConfigPath(string $path): ApplicationConfigurationInterface;

    /**
     * Sets the environments specific configuration folder path.
     *
     * @param string $path
     *
     * @return ApplicationConfigurationInterface
     */
    public function useEnvConfigPath(string $path): ApplicationConfigurationInterface;

    /**
     * @return string the top most root folder path.
     */
    public function getRootPath(): string;

    /**
     * @return string the application's base folder path
     */
    public function getBasePath(): string;

    /**
     * @return string the runtime folder path.
     */
    public function getRuntimePath(): string;

    /**
     * @return string the composer's vendor folder path.
     */
    public function getVendorPath(): string;

    /**
     * @return string the configuration folder path.
     */
    public function getConfigPath(): string;

    /**
     * @return string the environments specific configuration folder.
     */
    public function getEnvConfigPath(): string;

    /**
     * Builds and returns the configuration based on the paths provided.
     *
     * @param string $name  the configuration name to load. The name of the configuration must be a sub-folder name
     *                      under the configuration directory
     * @param bool   $cache whether to cache results or not
     *
     * @return array
     */
    public function build(string $name, bool $cache = false): array;
}
