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
 * APC Storage class
 *
 * @package    \Wurfl\Storage
 */
/**
 * Class Apc
 *
 * @package WurflCache\Adapter
 */
class Apc extends AbstractAdapter implements AdapterInterface
{
    /**
     *
     */
    const EXTENSION_MODULE_NAME = 'apc';

    /**
     * @var array
     */
    private $defaultParams
        = array(
            'namespace'       => 'wurfl',
            'cacheExpiration' => 0
        );

    /**
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        $this->ensureModuleExistence();

        $currentParams = $this->defaultParams;

        if (is_array($params) && !empty($params)) {
            $currentParams = array_merge($this->defaultParams, $params);
        }

        $this->namespace       = $currentParams['namespace'];
        $this->cacheExpiration = $currentParams['cacheExpiration'];
    }

    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool   $success
     * @param  mixed  $casToken
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $cacheId = $this->normalizeKey($key);
        $success = false;

        $value = $this->extract(apc_fetch($cacheId));
        if ($value === null) {
            return null;
        }

        $success = true;
        return $value;
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
        $cacheId = $this->normalizeKey($cacheId);

        return apc_exists($cacheId);
    }

    /**
     * Store an item.
     *
     * @param  string $cacheId
     * @param  mixed  $value
     *
     * @return bool
     */
    public function setItem($cacheId, $value)
    {
        $cacheId = $this->normalizeKey($cacheId);

        return apc_store(
            $cacheId, $this->compact($value), $this->cacheExpiration
        );
    }

    /**
     * Remove an item.
     *
     * @param  string $cacheId
     *
     * @return bool
     */
    public function removeItem($cacheId)
    {
        $cacheId = $this->normalizeKey($cacheId);

        return apc_delete($cacheId);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return apc_clear_cache('user');
    }

    /**
     * Ensures the existence of the the PHP Extension apc
     *
     * @throws Exception required extension is unavailable
     */
    private function ensureModuleExistence()
    {
        if (!(extension_loaded(self::EXTENSION_MODULE_NAME) && ini_get('apc.enabled') == true)) {
            throw new Exception ('The PHP extension apc must be installed, loaded and enabled.');
        }
    }
}