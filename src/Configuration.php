<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config;

use Da\Config\Contracts\ApplicationConfigurationInterface;
use Da\Config\Contracts\ConfigurationInterface;
use Da\Config\Contracts\EnvInterface;
use Da\Config\Contracts\FilesystemInterface;
use Da\Config\Exception\BadMethodCallException;
use Da\Config\Support\ApplicationConfiguration;
use Da\Config\Support\Env;
use Da\Config\Support\Filesystem;
use Da\Config\Support\Str;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ReflectionContainer;

/**
 *
 * Class Configuration.
 *
 * Main container aware factory class.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var Container
     */
    private static $container;

    /**
     * @inheritdoc
     */
    public static function make(string $class)
    {
        if (!static::getContainer()->has($class)) {
            throw new BadMethodCallException("Unrecognized class name '{$class}''.");
        }

        return static::getContainer()->get($class);
    }

    /**
     * @inheritdoc
     */
    public static function env(): EnvInterface
    {
        return static::make(EnvInterface::class);
    }

    /**
     * @inheritdoc
     */
    public static function app(): ApplicationConfigurationInterface
    {
        return static::make(ApplicationConfigurationInterface::class);
    }

    /**
     * @inheritdoc
     */
    public static function fs(): FilesystemInterface
    {
        return static::make(FilesystemInterface::class);
    }

    /**
     * @inheritdoc
     */
    public static function str(): Str
    {
        return static::make(Str::class);
    }

    /**
     * @inheritdoc
     */
    public static function getContainer(): ContainerInterface
    {
        if (null === static::$container) {
            static::$container = new Container();
            static::$container->delegate(new ReflectionContainer());
            static::$container->share(ApplicationConfigurationInterface::class, ApplicationConfiguration::class);
            static::$container->add(FilesystemInterface::class, Filesystem::class);
            static::$container->add(Str::class);
            static::$container
                ->share(EnvInterface::class, Env::class)
                ->withArguments([ApplicationConfigurationInterface::class, FilesystemInterface::class]);
        }

        return static::$container;
    }
}
