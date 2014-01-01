<?php
namespace WurflCache\Utils;

    /**
     * Copyright (c) 2012 ScientiaMobile, Inc.
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU Affero General Public License as
     * published by the Free Software Foundation, either version 3 of the
     * License, or (at your option) any later version.
     * Refer to the COPYING.txt file distributed with this package.
     *
     * @category   WURFL
     * @package    WURFL
     * @copyright  ScientiaMobile, Inc.
     * @license    GNU Affero General Public License
     * @version    $id$
     */
/**
 * WURFL File Utilities
 *
 * @package    WURFL
 */
class FileUtils
{
    /**
     * Create a directory structure recursiveley
     *
     * @param string $path
     * @param int    $mode
     */
    public static function mkdir($path, $mode = 0755)
    {
        @mkdir($path, $mode, true);
    }

    /**
     * Recursiely remove all files from the given directory NOT including the
     * specified directory itself
     *
     * @param string $path Directory to be cleaned out
     */
    public static function rmdirContents($path)
    {
        $files = scandir($path);
        array_shift($files); // remove '.' from array
        array_shift($files); // remove '..' from array

        foreach ($files as $file) {
            $file = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                self::rmdirContents($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }

    /**
     * Alias to rmdirContents()
     *
     * @param string $path Directory to be cleaned out
     *
     * @see rmdirContents()
     */
    public static function rmdir($path)
    {
        self::rmdirContents($path);
    }

    /**
     * Returns the unserialized contents of the given $file
     *
     * @param string $file filename
     *
     * @return mixed Unserialized data or null if file does not exist
     */
    public static function read($file)
    {
        if (!is_readable($file) || !is_file($file)) {
            return null;
        }

        $data = file_get_contents($file);

        if ($data === false) {
            return null;
        }

        return $data;
    }

    /**
     * Serializes and saves $data in the file $path and sets the last modified time to $mtime
     *
     * @param string  $path  filename to save data in
     * @param mixed   $data  data to be serialized and saved
     * @param integer $mtime Last modified date in epoch time
     *
     * @return bool
     */
    public static function write($path, $data, $mtime = 0)
    {
        if (!file_exists(dirname($path))) {
            self::mkdir(dirname($path), 0755);
        }

        $lock = LOCK_EX;

        if (false !== strpos($path, '://')) {
            $parts = explode('://', $path);

            // workaround for vfsStream
            if ($parts[0] === 'vfs') {
                $lock = 0;
            }
        }

        $contentWritten = file_put_contents($path, $data, $lock);
var_dump(vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure());
        if (!file_exists($path)) {
            return false;
        }

        if ($contentWritten && LOCK_EX === $lock) {
            // does not work with vfs stream
            chmod($path, 0777);
        }

        if ($contentWritten) {
            $mtime = ($mtime > 0) ? $mtime : time();
            touch($path, $mtime);
        }

        return (boolean) $contentWritten;
    }

    /**
     * Combines given array of $strings into a proper filesystem path
     *
     * @param array $strings Array of (string)path members
     *
     * @return string Proper filesystem path
     */
    public static function join($strings = array())
    {
        return implode(DIRECTORY_SEPARATOR, $strings);
    }
}
