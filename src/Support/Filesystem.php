<?php
namespace SideKit\Config\Support;

use DirectoryIterator;
use ErrorException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SideKit\Config\Contracts\FilesystemInterface;
use SideKit\Config\Exception\FileNotFoundException;
use SideKit\Config\Exception\InvalidArgumentException;

/**
 * Class Filesystem
 *
 * Eases the task to work with local files and directories.
 */
class Filesystem implements FilesystemInterface
{
    /**
     * Determine if a file or directory exists.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string $path
     * @param  bool $lock
     *
     * @throws FileNotFoundException
     *
     * @return string
     *
     */
    public function get($path, $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->getShared($path) : file_get_contents($path);
        }

        throw new FileNotFoundException("File does not exist at path {$path}");
    }

    /**
     * Read the contents of a file as an array of lines.
     *
     * @param string $path
     *
     * @return array
     */
    public function readLines($path)
    {
        // Read file into an array of lines with auto-detected line endings
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string $path
     *
     * @return string
     */
    public function getShared($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Get the returned value of a file.
     *
     * @param  string $path
     *
     * @throws FileNotFoundException
     *
     * @return mixed
     *
     */
    public function getRequiredFileValue($path)
    {
        if ($this->isFile($path)) {
            return require $path;
        }

        throw new FileNotFoundException("File does not exist at path {$path}");
    }

    /**
     * Require the given file once.
     *
     * @param  string $file
     *
     * @return mixed
     */
    public function requireOnce($file)
    {
        require_once $file;
    }

    /**
     * Write the contents of a file.
     *
     * @param  string $path
     * @param  string $contents
     * @param  bool $lock
     *
     * @return int
     */
    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Prepend to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function prepend($path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }

        return $this->put($path, $data);
    }

    /**
     * Append to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function append($path, $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array $paths
     *
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (!@unlink($path)) {
                    $success = false;
                }
            } catch (ErrorException $e) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Extract the file name from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string $path
     *
     * @return string
     */
    public function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string $path
     *
     * @return string|false
     */
    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string $path
     *
     * @return int
     */
    public function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string $path
     *
     * @return int
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string $directory
     *
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string $file
     *
     * @return bool
     */
    public function isFile($file)
    {
        return is_file($file);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param  string $pattern
     * @param  int $flags
     *
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string $directory
     *
     * @return array
     */
    public function files($directory)
    {
        $glob = glob($directory . '/*');

        if ($glob === false) {
            return [];
        }

        // To get the appropriate files, we'll simply glob the directory and filter
        // out any "files" that are not truly files so we do not end up with any
        // directories in our list, but only true files within the directory.
        return array_filter(
            $glob,
            function ($file) {
                return filetype($file) === 'file';
            }
        );
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param string $directory
     * @param string $pattern
     * @param boolean $ignoreDotFiles
     *
     * @return array
     */
    public function allFiles($directory, $pattern = '/^.*\.*$/i', $ignoreDotFiles = true)
    {
        if (!$this->isDirectory($directory)) {
            throw new InvalidArgumentException("The directory argument must be a directory: $directory");
        }
        $dirIterator = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        $files = [];

        foreach ($iterator as $file) {
            if ($ignoreDotFiles && $file->getBasename()[0] === '.') {
                continue;
            }
            if ($file->isFile() && preg_match($pattern, $file->getFilename())) {
                $name = $this->name($file->getBasename());
                $files[$name] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     * @param boolean $ignoreDotDirectories whether to ignore the dotted directories or not.
     *
     * @return array
     */
    public function directories($directory, $ignoreDotDirectories = true)
    {
        if (!$this->isDirectory($directory)) {
            throw new InvalidArgumentException("The directory argument must be a directory: $directory");
        }

        $directories = [];
        foreach ((new DirectoryIterator($directory)) as $file) {
            if ($ignoreDotDirectories && $file->isDot()) {
                continue;
            }
            if ($file->isDir()) {
                $directories[$file->getBasename()] = $file->getPathname();
            }
        }

        return $directories;
    }

    /**
     * Create a directory.
     *
     * @param  string $path
     * @param  int $mode
     * @param  bool $recursive
     * @param  bool $force
     *
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }
}
