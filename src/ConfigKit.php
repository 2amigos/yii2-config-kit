<?php
namespace SideKit\Config;

use League\Container\Container;
use League\Container\ReflectionContainer;
use SideKit\Config\Contracts\ConfigurationInterface;
use SideKit\Config\Contracts\FilesystemInterface;
use SideKit\Config\Exception\BadMethodCallException;
use SideKit\Config\Support\Configuration;
use SideKit\Config\Support\Env;
use SideKit\Config\Support\Filesystem;
use SideKit\Config\Support\Str;

/**
 *
 * Class ConfigKit.
 *
 * Main container aware factory class.
 */
class ConfigKit
{
    /**
     * @var Container
     */
    private static $container;

    /**
     * Returns an instance of
     *
     * @param $class
     *
     * @return mixed|object
     */
    public static function make($class)
    {
        if (!static::getContainer()->has($class)) {
            throw new BadMethodCallException("Unrecognized class name '{$class}''.");
        }

        return static::getContainer()->get($class);
    }

    /**
     * @return Env
     */
    public static function env()
    {
        return static::make(Env::class);
    }

    /**
     * @return Configuration
     */
    public static function config()
    {
        return static::make(ConfigurationInterface::class);
    }

    /**
     * @return Filesystem
     */
    public static function filesystem()
    {
        return static::make(FilesystemInterface::class);
    }

    /**
     * @return Str
     */
    public static function str()
    {
        return static::make(Str::class);
    }

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (static::$container === null) {
            static::$container = new Container();
            static::$container->delegate(new ReflectionContainer());
            static::$container->share(ConfigurationInterface::class, Configuration::class);
            static::$container->add(FilesystemInterface::class, Filesystem::class);
            static::$container->add(Str::class);
            static::$container->share(Env::class)
                ->withArguments([ConfigurationInterface::class, FilesystemInterface::class]);
        }

        return static::$container;
    }
}
