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

/**
 * Class Env
 *
 * Loads the global settings included in a .env file. Is using 'Dotenv\Dotenv' internally.
 */
interface EnvInterface
{
    /**
     * Returns a specific environment value.
     *
     * @param string $key     the environment value name
     * @param null   $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Checks whether is an specific environment.
     *
     * @param string $env
     *
     * @return bool
     */
    public function is(string $env): bool;

    /**
     * @return bool
     */
    public function isRunningInConsole(): bool;

    /**
     * @return bool
     */
    public function isRunningTests(): bool;

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function getEnvironmentPath(): string;

    /**
     * Set the directory for the environment file.
     *
     * @param string $path
     *
     * @return EnvInterface
     */
    public function useEnvironmentPath(string $path): EnvInterface;

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param string $file
     *
     * @return EnvInterface
     */
    public function loadEnvironmentFrom(string $file): EnvInterface;

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function getEnvironmentFile(): string;

    /**
     * Get the full path of the environment file.
     *
     * @return string
     */
    public function getEnvironmentFilePath(): string;

    /**
     * Loads environment values and ensures some that are required exists.
     *
     */
    public function load(): void;

    /**
     * Overloads environment values and ensures some that are required exists. Useful method for testing purposes.
     *
     */
    public function overload(): void;

    /**
     * @param array $data changes the environment file
     *
     * @return bool
     */
    public function changeEnvironmentFile(array $data = []): bool;
}
