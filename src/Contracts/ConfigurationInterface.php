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

use Da\Config\Exception\BadMethodCallException;
use Da\Config\Support\Str;
use League\Container\ContainerInterface;

/**
 *
 * Class ConfigurationInterface.
 *
 * Main container aware factory class.
 */
interface ConfigurationInterface
{
    /**
     * Returns an instance of previously configured Container definition.
     *
     * @param string $class
     *
     * @throws BadMethodCallException
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     * @throws \Psr\Container\NotFoundExceptionInterface  Error while retrieving the entry.
     * @return mixed|object
     */
    public static function make(string $class);

    /**
     * Returns an Env instance that is aware of the environment configuration params
     *
     * @throws BadMethodCallException
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     * @throws \Psr\Container\NotFoundExceptionInterface  Error while retrieving the entry.
     * @return EnvInterface
     */
    public static function env(): EnvInterface;

    /**
     * Returns Application configuration settings.
     *
     * @throws BadMethodCallException
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     * @throws \Psr\Container\NotFoundExceptionInterface  Error while retrieving the entry.
     *
     * @return ApplicationConfigurationInterface
     */
    public static function app(): ApplicationConfigurationInterface;

    /**
     * Returns an instance of the Filesystem class.
     *
     * @throws BadMethodCallException
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     * @throws \Psr\Container\NotFoundExceptionInterface  Error while retrieving the entry.
     * @return FilesystemInterface
     */
    public static function fs(): FilesystemInterface;

    /**
     * Returns an instance of the Str utility class.
     *
     * @throws BadMethodCallException
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     * @throws \Psr\Container\NotFoundExceptionInterface  Error while retrieving the entry.
     * @return Str
     */
    public static function str(): Str;

    /**
     * Returns an instance of the League Container
     *
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface;
}
