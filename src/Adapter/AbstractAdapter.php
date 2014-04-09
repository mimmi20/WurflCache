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
/**
 * Class AbstractAdapter
 *
 * @package WurflCache\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * the time until the cache expires
     *
     * @var integer
     */
    protected $cacheExpiration = 86400;

    /**
     * the namespace used to build the internal cache id
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * @var int
     */
    protected $expiration = 0;

    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool   $success
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null)
    {
        $success = false;
        return null;
    }

    /**
     * save the content into the zend cache
     *
     * @param string $cacheId The cache id
     * @param mixed  $content The content to store
     *
     * @return boolean whether the content was stored
     */
    public function setItem($cacheId, $content)
    {
        return true;
    }

    /**
     * Test if an item exists.
     *
     * @param  string $cacheId
     *
     * @return bool
     */
    public function hasItem($cacheId)
    {
        return false;
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
        return true;
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return true;
    }

    /**
     * set the cacheExpiration time
     *
     * @param int $expiration
     *
     * @return AdapterInterface
     */
    public function setExpiration($expiration = 86400)
    {
        $this->cacheExpiration = (int) $expiration;

        return $this;
    }

    /**
     * set the cache namespace
     *
     * @param string $namespace
     *
     * @return AdapterInterface
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * normalizes the cache id for the cache
     *
     * @param string $cacheId The cache id
     *
     * @return string The formated cache id
     */
    protected function normalizeKey($cacheId)
    {
        return Helper\IdGenerator::encode($this->namespace, $cacheId);
    }

    /**
     * compacts the content for the cache
     *
     * @param mixed $content
     *
     * @return string
     */
    protected function compact($content)
    {
        /** @var $object Helper\StorageObject */
        $object = new Helper\StorageObject($content, $this->cacheExpiration);
        return serialize($object);
    }

    /**
     * compacts the content for the cache
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function extract($value)
    {
        /** @var $object Helper\StorageObject */
        $object = unserialize($value);
        if ($value === $object) {
            return null;
        }

        if (!($object instanceof Helper\StorageObject)) {
            return null;
        }

        if ($object->isExpired()) {
            return null;
        }

        return $object->value();
    }
}
