<?php
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

namespace WurflCache\Utils;

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
     *
     * @return bool
     */
    public static function rmdir($path)
    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            $file = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($file)) {
                self::rmdir($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }

        return rmdir($path);
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

        list($stream, $lock) = self::detectStream($path);

        $contentWritten = file_put_contents($path, $data, $lock);
        $limitedStreams = array('vfs');

        if (!file_exists($path) || !$contentWritten) {
            return false;
        }

        if (!in_array($stream, $limitedStreams)) {
            // does not work with vfs stream
            chmod($path, 0777);
        }

        if (!in_array($stream, $limitedStreams) || version_compare(PHP_VERSION, '5.4.0', '>=')) {
            // does not work with vfs stream on PHP 5.3
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
    public static function join(array $strings = array())
    {
        return implode(DIRECTORY_SEPARATOR, $strings);
    }

    /**
     * detects if the the path is linked to an file stream
     *
     * @param $path
     *
     * @return array
     */
    private static function detectStream($path)
    {
        $lock   = LOCK_EX;
        $stream = 'file';

        if (false !== strpos($path, '://')) {
            $parts  = explode('://', $path);
            $stream = $parts[0];

            // workaround for vfsStream
            if ($stream === 'vfs') {
                $lock = 0;
            }
        }

        return array($stream, $lock);
    }
}
