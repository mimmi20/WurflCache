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
     * WURFL Storage
     *
     * @package    \Wurfl\Storage
     */
/**
 * Class Memcache
 *
 * @package WurflCache\Adapter
 */
class Memcache extends AbstractAdapter implements AdapterInterface
{
    /**
     *
     */
    const EXTENSION_MODULE_NAME = 'memcache';
    /**
     *
     */
    const DEFAULT_PORT = 11211;

    /**
     * @var \Memcache
     */
    private $memcache;
    /**
     * @var
     */
    private $host;
    /**
     * @var
     */
    private $port;

    /**
     * @var array
     */
    private $defaultParams
        = array(
            'host'            => '127.0.0.1',
            'port'            => self::DEFAULT_PORT,
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

        $this->toFields($currentParams);
        $this->initializeMemCache();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->memcache = null;
    }

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
        $cacheId = $this->normalizeKey($key);
        $success = false;

        $value = $this->extract($this->memcache->get($cacheId));
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
        $tempData = $this->memcache->set(
            $cacheId, 
            ''
            0,
            $this->cacheExpiration
        );
        
        if (false === $tempData) {
            return true;
        }
        
        $this->removeItem($cacheId);
        
        return false;
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

        return $this->memcache->set(
            $cacheId, 
            $this->compact($value), 
            0, 
            $this->cacheExpiration
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
        return $this->memcache->delete($cacheId);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return $this->memcache->flush();
    }

    /**
     * @param $params
     */
    private function toFields($params)
    {
        foreach ($params as $cacheId => $value) {
            $this->$cacheId = $value;
        }
    }

    /**
     * Initializes the Memcache Module

     */
    private function initializeMemCache()
    {
        $this->memcache = new \Memcache();

        // support multiple hosts using semicolon to separate hosts
        $hosts = explode(';', $this->host);

        // different ports for each hosts the same way
        $ports = explode(';', $this->port);

        if (count($ports) < 1) {
            $ports = array_fill(0, count($hosts), self::DEFAULT_PORT);
        } elseif (count($ports) === 1) {
            // if we have just one port, use it for all hosts
            $usedPort = $ports[0];
            $ports    = array_fill(0, count($hosts), $usedPort);
        }

        foreach ($hosts as $i => $host) {
            if (!isset($ports[$i])) {
                /*
                 * if we have a difference between the count of hosts and
                 * the count of ports, use the default port to fill the gap
                 */
                $ports[$i] = self::DEFAULT_PORT;
            }

            $this->memcache->addServer($host, $ports[$i]);
        }
    }

    /**
     * Ensures the existence of the the PHP Extension memcache
     *
     * @throws Exception required extension is unavailable
     */
    private function ensureModuleExistence()
    {
        if (!extension_loaded(self::EXTENSION_MODULE_NAME)) {
            throw new Exception(
                'The PHP extension memcache must be installed and loaded in order to use the Memcached.'
            );
        }
    }
}
