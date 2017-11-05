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

use Da\Config\Exception\FileNotFoundException;

interface FilesystemInterface
{
    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param bool   $lock
     *
     * @throws FileNotFoundException
     *
     * @return string
     *
     */
    public function get(string $path, bool $lock = false): string;

    /**
     * Read the contents of a file as an array of lines.
     *
     * @param string $path
     *
     * @return array
     */
    public function readLines(string $path): array;

    /**
     * Get contents of a file with shared access.
     *
     * @param string $path
     *
     * @return string
     */
    public function getShared(string $path): string;

    /**
     * Get the returned value of a 'required' inserted file.
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return mixed
     *
     */
    public function getRequiredFileValue(string $path);

    /**
     * Require the given file once.
     *
     * @param string $file
     *
     * @return mixed
     */
    public function requireOnce(string $file);

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @param bool   $lock
     *
     * @return int
     */
    public function put(string $path, string $contents, bool $lock = false): int;

    /**
     * Prepend to a file.
     *
     * @param string $path
     * @param string $data
     *
     * @return int
     */
    public function prepend(string $path, string $data): int;

    /**
     * Append to a file.
     *
     * @param string $path
     * @param string $data
     *
     * @return int
     */
    public function append(string $path, string $data): int;

    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     *
     * @return bool
     */
    public function delete($paths): bool;

    /**
     * Extract the file name from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public function name(string $path): string;

    /**
     * Extract the trailing name component from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public function basename(string $path): string;

    /**
     * Extract the parent directory from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public function dirname(string $path): string;

    /**
     * Extract the file extension from a file path.
     *
     * @param string $path
     *
     * @return string
     */
    public function extension(string $path): string;

    /**
     * Get the file type of a given file.
     *
     * @param string $path
     *
     * @return string
     */
    public function type(string $path): string;

    /**
     * Get the mime-type of a given file.
     *
     * @param string $path
     *
     * @return false|string
     */
    public function mimeType(string $path);

    /**
     * Get the file size of a given file.
     *
     * @param string $path
     *
     * @return int
     */
    public function size(string $path): int;

    /**
     * Get the file's last modification time.
     *
     * @param string $path
     *
     * @return int
     */
    public function lastModified(string $path): int;

    /**
     * Determine if the given path is a directory.
     *
     * @param string $directory
     *
     * @return bool
     */
    public function isDirectory(string $directory): bool;

    /**
     * Determine if the given path is readable.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isReadable(string $path): bool;

    /**
     * Determine if the given path is writable.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isWritable(string $path): bool;

    /**
     * Determine if the given path is a file.
     *
     * @param string $file
     *
     * @return bool
     */
    public function isFile(string $file): bool;

    /**
     * Find path names matching a given pattern.
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return array
     */
    public function glob(string $pattern, int $flags = 0): array;

    /**
     * Get an array of all files in a directory.
     *
     * @param string $directory
     *
     * @return array
     */
    public function files(string $directory): array;

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param string  $directory
     * @param string  $pattern
     * @param boolean $ignoreDotFiles
     *
     * @return array
     */
    public function allFiles(string $directory, string $pattern = '/^.*\.*$/i', bool $ignoreDotFiles = true): array;

    /**
     * Get all of the directories within a given directory.
     *
     * @param string  $directory
     * @param boolean $ignoreDotDirectories whether to ignore the dotted directories or not.
     *
     * @return array
     */
    public function directories(string $directory, bool $ignoreDotDirectories = true): array;

    /**
     * Create a directory.
     *
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     * @param bool   $force
     *
     * @return bool
     */
    public function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false): bool;
}
