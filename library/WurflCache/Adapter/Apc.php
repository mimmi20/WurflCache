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
class Apc implements AdapterInterface
{
    const EXTENSION_MODULE_NAME = 'apc';
    private $currentParams = array(
        'namespace'  => 'wurfl',
        'expiration' => 0
    );

    protected $is_volatile = true;

    public function __construct(array $params = array())
    {
        if (is_array($params)) {
            array_merge($this->currentParams, $params);
        }

        $this->initialize();
    }

    private function initialize()
    {
        $this->ensureModuleExistence();
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
        $value = apc_fetch($this->encode($this->apcNameSpace(), $key));

        return ($value !== false) ? $value : null;
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
        $value = apc_store(
            $this->encode($this->apcNameSpace(), $key), $value,
            $this->expire()
        );
        if ($value === false) {
            return false;
        }

        return true;
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
        return apc_delete($this->encode($this->apcNameSpace(), $key));
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
     * Remove expired items
     *
     * @return bool
     */
    public function clearExpired()
    {
        return null;
    }

    private function apcNameSpace()
    {
        return $this->currentParams['namespace'];
    }

    private function expire()
    {
        return $this->currentParams['expiration'];
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

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     * @param string $namespace
     * @param string $input
     *
     * @return string $input with the given $namespace as a prefix
     */
    private function encode($namespace, $input)
    {
        return implode(':', array('Wurfl', $namespace, $input));
    }
}