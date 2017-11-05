<?php

/*
 * This file is part of the 2amigos/yii2-config-kit project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Config\Support;

use Closure;
use Da\Config\Contracts\ApplicationConfigurationInterface;
use Da\Config\Contracts\EnvInterface;
use Da\Config\Contracts\FilesystemInterface;
use Dotenv\Dotenv;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Class Env
 *
 * Loads the global settings included in a .env file. Is using 'Dotenv\Dotenv' internally.
 */
class Env implements EnvInterface
{
    /**
     * The custom environment path defined by the developer.
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = '.env';
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;
    /**
     * @var ApplicationConfigurationInterface
     */
    protected $configuration;
    /**
     * @var Str
     */
    protected $str;

    /**
     * @inheritdoc
     */
    public function __construct(
        ApplicationConfigurationInterface $configuration,
        FilesystemInterface $filesystem,
        Str $str = null,
        $environmentPath = null
    ) {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
        $this->str = $str ?? new Str();
        if ($environmentPath) {
            $this->useEnvironmentPath($environmentPath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default instanceof Closure ? $default() : $default;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function is(string $env): bool
    {
        return $this->str->is($env, $this->get('YII_ENV'));
    }

    /**
     * @inheritdoc
     */
    public function isRunningInConsole(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * @inheritdoc
     */
    public function isRunningTests(): bool
    {
        return $this->get('YII_ENV') === 'test';
    }

    /**
     * @inheritdoc
     */
    public function getEnvironmentPath(): string
    {
        return $this->environmentPath ?: $this->configuration->getRootPath();
    }

    /**
     * @inheritdoc
     */
    public function useEnvironmentPath(string $path): EnvInterface
    {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function loadEnvironmentFrom(string $file): EnvInterface
    {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function getEnvironmentFile(): string
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * @inheritdoc
     */
    public function getEnvironmentFilePath(): string
    {
        return $this->getEnvironmentPath() . '/' . $this->getEnvironmentFile();
    }

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        $this->checkForSpecificEnvironmentFile();

        $dotEnv = new Dotenv($this->getEnvironmentPath(), $this->getEnvironmentFile());

        $dotEnv->load();

        $this->validateEnvironmentFile($dotEnv);
    }

    /**
     * @inheritdoc
     */
    public function overload(): void
    {
        $this->checkForSpecificEnvironmentFile();

        $dotEnv = new Dotenv($this->getEnvironmentPath(), $this->getEnvironmentFile());

        $dotEnv->overload();

        $this->validateEnvironmentFile($dotEnv);
    }

    /**
     * @inheritdoc
     */
    public function changeEnvironmentFile(array $data = []): bool
    {
        if (empty($data)) {
            return false;
        }

        $lines = $this->filesystem->readLines($this->getEnvironmentFilePath());
        $content = [];

        foreach ($data as $key => $value) {
            foreach ($lines as $idx => $line) {
                if ($this->looksLikeSetter($line)) {
                    $entry = array_map('trim', explode('=', $line, 2));
                    $content[$idx] = $this->str->is($entry[0], $key) ? $key . '=' . $value : $line;
                } else {
                    $content[$idx] = $line;
                }
            }
        }

        $content = implode("\n", $content);

        return $this->filesystem->put($this->getEnvironmentFilePath(), $content);
    }

    /**
     * Validates loaded environment.
     *
     * @param Dotenv $env
     */
    protected function validateEnvironmentFile(Dotenv $env): void
    {
        // YII
        $env->required('YII_DEBUG')->allowedValues(['', '0', '1', 'true', true, 'false', false]);
        $env->required('YII_ENV')->allowedValues(['local', 'dev', 'prod', 'test', 'stage']);

        // APP
        $env->required(['APP_NAME', 'APP_ADMIN_EMAIL', 'APP_NAME']);

        // DATABASE
        $env->required(['DATABASE_DSN', 'DATABASE_USER', 'DATABASE_PASSWORD']);

        // CONFIG
        $env->required(['CONFIG_USE_CACHE'])->allowedValues(['', '0', '1', 'true', true, 'false', false]);
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
     *
     */
    protected function checkForSpecificEnvironmentFile(): void
    {
        if (isset($_SERVER['argv']) && $this->isRunningInConsole()) {
            $input = new ArgvInput();

            if ($input->hasParameterOption('--env')) {
                $file = $this->getEnvironmentFile() . '.' . $input->getParameterOption('--env');

                $this->loadEnvironmentFile($file);
            }
        }

        if (!$this->get('APP_ENV')) {
            return;
        }

        if (empty($file)) {
            $file = $this->getEnvironmentFile() . '.' . $this->get('APP_ENV');
            $this->loadEnvironmentFile($file);
        }
    }

    /**
     * Load a custom environment file.
     *
     * @param string $file
     *
     */
    protected function loadEnvironmentFile($file): void
    {
        if ($this->filesystem->exists($this->getEnvironmentPath() . '/' . $file)) {
            $this->loadEnvironmentFrom($file);
        }
    }

    /**
     * Determine if the line in the file is a comment, e.g. begins with a #.
     *
     * @param string $line
     *
     * @return bool
     */
    protected function isComment(string $line): bool
    {
        return strpos(ltrim($line), '#') === 0;
    }

    /**
     * Determine if the given line looks like it's setting a variable.
     *
     * @param string $line
     *
     * @return bool
     */
    protected function looksLikeSetter(string $line): bool
    {
        return strpos($line, '=') !== false;
    }
}
