<?php
namespace WurflCache\Adapter;

/**
 * Copyright (c) 2012 ScientiaMobile, Inc.
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @category   WURFL
 * @package    \Wurfl\Storage
 * @copyright  ScientiaMobile, Inc.
 * @license    GNU Affero General Public License
 * @author     Fantayeneh Asres Gizaw
 * @version    $id$
 */
use WurflCache\Utils\FileUtils;

/**
 * WURFL Storage
 *
 * @package    \Wurfl\Storage
 */
class File
    extends AbstractAdapter
{
    /**
     * @var array
     */
    private $defaultParams = array(
        'dir'        => '/tmp',
        'expiration' => 0,
        'readonly'   => 'false',
    );

    /**
     * @var
     */
    private $root;
    /**
     * @var
     */
    private $readonly;

    /**
     *
     */
    const DIR = 'dir';

    /**
     * @param $params
     */
    public function __construct($params)
    {
        $currentParams = $this->defaultParams;

        if (is_array($params) && !empty($params)) {
            $currentParams = array_merge(
                $this->defaultParams,
                $params
            );
        }
        $this->initialize($currentParams);
    }

    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool   $success
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem(
        $key,
        & $success = null
    ) {
        $success = false;

        if (!$this->hasItem($key)) {
            return null;
        }

        $path = $this->keyPath($key);

        /** @var $value Helper\StorageObject */
        $value = $this->extract(FileUtils::read($path));
        if ($value === null) {
            return null;
        }

        $success = true;

        return $value;
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function hasItem($key)
    {
        $path = $this->keyPath($key);

        return file_exists($path) && is_file($path) && is_readable($path);
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return bool
     */
    public function setItem(
        $key,
        $value
    ) {
        $path = $this->keyPath($key);
        FileUtils::write(
            $path,
            $this->compact($value)
        );
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function removeItem($key)
    {
        $path = $this->keyPath($key);

        return unlink($path);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        FileUtils::rmdirContents($this->root);
    }

    /**
     * @param $params
     */
    private function initialize($params)
    {
        $this->root            = $params[self::DIR];
        $this->cacheExpiration = $params['expiration'];
        $this->readonly        = ($params['readonly'] == 'true' || $params['readonly'] === true);

        $this->createRootDirIfNotExist();
    }

    /**
     * @throws Exception
     */
    private function createRootDirIfNotExist()
    {
        if (!isset($this->root)) {
            throw new Exception(
                'You have to provide a path to read/store the browscap cache file',
                Exception::CACHE_DIR_MISSING
            );
        }

        $filename = '';

        if (false === realpath($this->root)) {
            /*
             * the cache path is mostly invalid
             * -> check if its really a filename
             */
            if (substr($this->root, -4) === '.php' || substr($this->root, -4) === '.ini') {
                // extract file name
                $filename   = basename($this->root);
                $this->root = dirname($this->root);
            }
        }

        $oldCacheDir = $this->root;

        // Is the cache dir really the directory or is it directly the file?
        if (is_file($this->root)) {
            $this->root = dirname($this->root);
        } elseif (!is_dir($this->root)) {
            @mkdir(
                $this->root,
                0777,
                true
            );

            if (!is_dir($this->root)) {
                throw new Exception(
                    'The file storage directory does not exist and could not be created. '
                    . 'Please make sure the directory is writeable: ' . $this->root
                );
            }
        }

        if (!is_readable($this->root)) {
            throw new Exception(
                'Its not possible to read from the given cache path "' . $this->root . '"',
                Exception::CACHE_DIR_NOT_READABLE
            );
        }

        if (!is_writable($this->root)) {
            throw new Exception(
                'Its not possible to write to the given cache path "' . $this->root . '"',
                Exception::CACHE_DIR_NOT_WRITABLE
            );
        }
    }

    /**
     * @param $key
     *
     * @return string
     */
    private function keyPath($key)
    {
        return FileUtils::join(array($this->root, $this->spread(md5($key))));
    }

    /**
     * @param string $md5
     * @param int    $splitCount
     *
     * @return string
     */
    private function spread(
        $md5,
        $splitCount = 2
    ) {
        $path = '';

        for ($i = 0; $i < $splitCount; $i++) {
            $path .= $md5 [$i] . DIRECTORY_SEPARATOR;
        }

        $path .= substr(
            $md5,
            $splitCount
        );

        return $path;
    }
}
