<?php
namespace SideKit\Config\Support;

use Closure;
use Dotenv\Dotenv;
use SideKit\Config\Contracts\ConfigurationInterface;
use SideKit\Config\Contracts\FilesystemInterface;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Class Env
 *
 * Loads the global settings included in a .env file. Is using 'Dotenv\Dotenv' internally.
 */
class Env
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
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var Str
     */
    protected $str;

    /**
     * Env constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param FilesystemInterface $filesystem
     * @param Str $str
     * @param null $environmentPath
     */
    public function __construct(
        ConfigurationInterface $configuration,
        FilesystemInterface $filesystem,
        Str $str = null,
        $environmentPath = null
    ) {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
        $this->str = isset($str) ? $str : new Str();
        if ($environmentPath) {
            $this->useEnvironmentPath($environmentPath);
        }
    }

    /**
     * Returns a specific environment value.
     *
     * @param string $key the environment value name
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default instanceof Closure ? $default() : $default;
        }

        return $value;
    }

    /**
     * Checks whether is an specific environment.
     *
     * @param string $env
     *
     * @return bool
     */
    public function is($env)
    {
        return $this->str->is($env, $this->get('YII_ENV'));
    }

    /**
     * @return bool
     */
    public function isRunningInConsole()
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * @return bool
     */
    public function isRunningTests()
    {
        return $this->get('YII_ENV') === 'test';
    }

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function getEnvironmentPath()
    {
        return $this->environmentPath ?: $this->configuration->getRootPath();
    }

    /**
     * Set the directory for the environment file.
     *
     * @param  string $path
     *
     * @return $this
     */
    public function useEnvironmentPath($path)
    {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param  string $file
     *
     * @return $this
     */
    public function loadEnvironmentFrom($file)
    {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function getEnvironmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * Get the full path of the environment file.
     *
     * @return string
     */
    public function getEnvironmentFilePath()
    {
        return $this->getEnvironmentPath() . '/' . $this->getEnvironmentFile();
    }

    /**
     * Loads environment values and ensures some that are required exists.
     *
     */
    public function load()
    {
        $this->checkForSpecificEnvironmentFile();

        $dotEnv = new Dotenv($this->getEnvironmentPath(), $this->getEnvironmentFile());

        $dotEnv->load();

        $this->validateEnvironmentFile($dotEnv);
    }

    /**
     * Overloads environment values and ensures some that are required exists. Useful method for testing purposes.
     *
     */
    public function overload()
    {
        $this->checkForSpecificEnvironmentFile();

        $dotEnv = new Dotenv($this->getEnvironmentPath(), $this->getEnvironmentFile());
        $dotEnv->overload();

        $this->validateEnvironmentFile($dotEnv);
    }

    /**
     * @param array $data changes the environment file
     *
     * @return bool
     */
    public function changeEnvironmentFile(array $data = [])
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
    protected function validateEnvironmentFile($env)
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
    protected function checkForSpecificEnvironmentFile()
    {
        if ($this->isRunningInConsole() && isset($_SERVER['argv'])) {
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
     * @param  string $file
     *
     */
    protected function loadEnvironmentFile($file)
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
    protected function isComment($line)
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
    protected function looksLikeSetter($line)
    {
        return strpos($line, '=') !== false;
    }
}
