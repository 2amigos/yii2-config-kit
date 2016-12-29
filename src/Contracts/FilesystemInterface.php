<?php
namespace SideKit\Config\Contracts;

use SideKit\Config\Exception\FileNotFoundException;

interface FilesystemInterface
{
    /**
     * Determine if a file or directory exists.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists($path);

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
    public function get($path, $lock = false);

    /**
     * Read the contents of a file as an array of lines.
     *
     * @param string $path
     *
     * @return array
     */
    public function readLines($path);

    /**
     * Get contents of a file with shared access.
     *
     * @param  string $path
     *
     * @return string
     */
    public function getShared($path);

    /**
     * Get the returned value of a 'required' inserted file.
     *
     * @param  string $path
     *
     * @throws FileNotFoundException
     *
     * @return mixed
     *
     */
    public function getRequiredFileValue($path);

    /**
     * Require the given file once.
     *
     * @param  string $file
     *
     * @return mixed
     */
    public function requireOnce($file);

    /**
     * Write the contents of a file.
     *
     * @param  string $path
     * @param  string $contents
     * @param  bool $lock
     *
     * @return int
     */
    public function put($path, $contents, $lock = false);

    /**
     * Prepend to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function prepend($path, $data);

    /**
     * Append to a file.
     *
     * @param  string $path
     * @param  string $data
     *
     * @return int
     */
    public function append($path, $data);

    /**
     * Delete the file at a given path.
     *
     * @param  string|array $paths
     *
     * @return bool
     */
    public function delete($paths);

    /**
     * Extract the file name from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function name($path);

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function basename($path);

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function dirname($path);

    /**
     * Extract the file extension from a file path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function extension($path);

    /**
     * Get the file type of a given file.
     *
     * @param  string $path
     *
     * @return string
     */
    public function type($path);

    /**
     * Get the mime-type of a given file.
     *
     * @param  string $path
     *
     * @return string|false
     */
    public function mimeType($path);

    /**
     * Get the file size of a given file.
     *
     * @param  string $path
     *
     * @return int
     */
    public function size($path);

    /**
     * Get the file's last modification time.
     *
     * @param  string $path
     *
     * @return int
     */
    public function lastModified($path);

    /**
     * Determine if the given path is a directory.
     *
     * @param  string $directory
     *
     * @return bool
     */
    public function isDirectory($directory);

    /**
     * Determine if the given path is readable.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function isReadable($path);

    /**
     * Determine if the given path is writable.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function isWritable($path);

    /**
     * Determine if the given path is a file.
     *
     * @param  string $file
     *
     * @return bool
     */
    public function isFile($file);

    /**
     * Find path names matching a given pattern.
     *
     * @param  string $pattern
     * @param  int $flags
     *
     * @return array
     */
    public function glob($pattern, $flags = 0);

    /**
     * Get an array of all files in a directory.
     *
     * @param  string $directory
     *
     * @return array
     */
    public function files($directory);

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param string $directory
     * @param string $pattern
     * @param boolean $ignoreDotFiles
     *
     * @return array
     */
    public function allFiles($directory, $pattern = '/^.*\.*$/i', $ignoreDotFiles = true);

    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     * @param boolean $ignoreDotDirectories whether to ignore the dotted directories or not.
     *
     * @return array
     */
    public function directories($directory, $ignoreDotDirectories = true);

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
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false);
}
