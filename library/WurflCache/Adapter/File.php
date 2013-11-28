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
class File implements AdapterInterface
{
    private $defaultParams = array(
        'dir'        => '/tmp',
        'expiration' => 0,
        'readonly'   => 'false',
    );

    private $expire;
    private $root;
    private $readonly;

    const DIR = 'dir';

    protected $supports_secondary_caching = true;

    public function __construct($params)
    {
        $currentParams = is_array($params) ? array_merge($this->defaultParams, $params) : $this->defaultParams;
        $this->initialize($currentParams);
    }
    /**
     * Get an item.
     *
     * @param  string  $key
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $path  = $this->keyPath($key);

        /** @var $value Helper\StorageObject */
        $value = FileUtils::read($path);
        if ($value === null) {
            $success = false;
            return null;
        }

        $success = true;
        return $this->unwrap($value, $path);
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasItem($key)
    {
        return null;
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     */
    public function setItem($key, $value)
    {
        $value = new Helper\StorageObject($value, $this->expire);
        $path  = $this->keyPath($key);
        FileUtils::write($path, $value);
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     */
    public function touchItem($key)
    {
        return null;
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     */
    public function removeItem($key)
    {
        return null;
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
     * Remove expired items
     *
     * @return bool
     */
    public function clearExpired()
    {
        return null;
    }

    private function initialize($params)
    {
        $this->root     = $params[self::DIR];
        $this->expire   = $params['expiration'];
        $this->readonly = ($params['readonly'] == 'true' || $params['readonly'] === true);

        $this->createRootDirIfNotExist();
    }

    private function createRootDirIfNotExist()
    {
        if (!is_dir($this->root)) {
            @mkdir($this->root, 0777, true);
            if (!is_dir($this->root)) {
                throw new Exception("The file storage directory does not exist and could not be created. Please make sure the directory is writeable: " . $this->root);
            }
        }
        if (!$this->readonly && !is_writeable($this->root)) {
            throw new Exception("The file storage directory is not writeable: " . $this->root);
        }
    }

    private function unwrap(Helper\StorageObject $value, $path)
    {
        if ($value->isExpired()) {
            unlink($path);

            return null;
        }

        return $value->value();
    }

    private function keyPath($key)
    {
        return FileUtils::join(array($this->root, $this->spread(md5($key))));
    }

    private function spread($md5, $n = 2)
    {
        $path = '';

        for ($i = 0; $i < $n; $i++) {
            $path .= $md5 [$i] . DIRECTORY_SEPARATOR;
        }

        $path .= substr($md5, $n);

        return $path;
    }
}
