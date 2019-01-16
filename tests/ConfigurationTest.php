<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Tests;

use Da\Config\Configuration;
use Da\Config\Support\ApplicationConfiguration;
use Da\Config\Support\Env;
use Da\Config\Support\Filesystem;
use Da\Config\Support\Str;
use League\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGetContainerReturnsAContainerInterface()
    {
        $this->assertInstanceOf(ContainerInterface::class, Configuration::getContainer());
        $this->assertInstanceOf(\Psr\Container\ContainerInterface::class, Configuration::getContainer());
    }

    public function testMethodsReturnExpectedInstances()
    {
        $this->assertInstanceOf(Str::class, Configuration::str());
        $this->assertInstanceOf(Filesystem::class, Configuration::fs());
        $this->assertInstanceOf(ApplicationConfiguration::class, Configuration::app());
        $this->assertInstanceOf(Env::class, Configuration::env());
    }

    public function testSharedAndNotSharedInstances()
    {
        $this->assertNotSame(Configuration::str(), Configuration::str());
        $this->assertNotSame(Configuration::fs(), Configuration::fs());
        $this->assertSame(Configuration::env(), Configuration::env());
        $this->assertSame(Configuration::app(), Configuration::app());
    }
}
