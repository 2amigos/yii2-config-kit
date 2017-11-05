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

interface ConfigurationBuilderInterface
{
    /**
     * Sets the directory where the configuration files are.
     *
     * @param string $path
     *
     * @return ConfigurationBuilderInterface
     */
    public function useDirectory(string $path): ConfigurationBuilderInterface;

    /**
     * Sets the cache file to store resulting config. If null, no configuration will be cached.
     *
     * @param $path
     *
     * @return ConfigurationBuilderInterface
     */
    public function useCacheDirectory(string $path): ConfigurationBuilderInterface;

    /**
     * Sets whether to cache the resulting configuration file or not.
     *
     * @param boolean $value
     *
     * @return ConfigurationBuilderInterface
     */
    public function useCache(bool $value): ConfigurationBuilderInterface;

    /**
     * Builds the configuration file for the application.
     *
     * @param string $name the environment name to build the configuration.
     *
     * @return array
     */
    public function build(string $name): array;
}
