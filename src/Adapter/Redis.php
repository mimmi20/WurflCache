<?php
/**
 * Copyright (c) 2015 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @category   WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license    GNU Affero General Public License
 * @author     Fantayeneh Asres Gizaw
 * @version    $id$
 */

namespace WurflCache\Adapter;

use Wurfl\WurflConstants;

/**
 * WURFL Storage
 */
class Redis extends AbstractAdapter
{
    const EXTENSION_MODULE_NAME = 'redis';

    protected $defaultParams = array(
        'host'            => '127.0.0.1',
        'port'            => '6379',
        'hash_name'       => 'WURFL_DATA',
        'redis'           => null,
        'database'        => 0,
        'client'          => 'phpredis',
        'namespace'       => 'wurfl',
        'cacheExpiration' => 0,
        'cacheVersion'     => WurflConstants::API_NAMESPACE,
    );

    private $database;
    private $host;
    private $port;
    private $redis;
    private $hashName;
    private $client;

    /**
     * Get an item.
     *
     * @param string $cacheId
     * @param bool   $success
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($cacheId, & $success = null)
    {
        $cacheId = $this->normalizeKey($cacheId);

        $storedValue = $this->redis->get($cacheId);

        if (false === $success) {
            return null;
        }

        $value = $this->extract($storedValue);
        if ($value === null) {
            $success = false;

            return null;
        }

        $success = true;

        return $value;
    }

    /**
     * Test if an item exists.
     *
     * @param string $cacheId
     *
     * @return bool
     */
    public function hasItem($cacheId)
    {
        $cacheId = $this->normalizeKey($cacheId);

        return (bool) $this->redis->exists($cacheId);
    }

    /**
     * Store an item.
     *
     * @param string $cacheId
     * @param mixed  $value
     *
     * @return bool
     */
    public function setItem($cacheId, $value)
    {
        $cacheId = $this->normalizeKey($cacheId);

        return (bool) $this->redis->set($cacheId, $this->compact($value), $this->cacheExpiration);
    }

    /**
     * Remove an item.
     *
     * @param string $cacheId
     *
     * @return bool
     */
    public function removeItem($cacheId)
    {
        $cacheId = $this->normalizeKey($cacheId);

        return (bool) $this->redis->del($cacheId);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return (bool) $this->redis->flushDB();
    }

    /**
     * Ensures the existence of the the PHP Extension apc
     *
     * @throws \WurflCache\Adapter\Exception required extension is unavailable
     */
    private function ensureModuleExistence($redis)
    {
        if (extension_loaded(self::EXTENSION_MODULE_NAME) && ($redis instanceof \Redis)) {
            return;
        }

        if (class_exists('\Predis\Client') && ($redis instanceof \Predis\Client)) {
            return;
        }

        throw new Exception('Connection object is not a Redis or a Predis\Client instance');
    }

    /**
     * @param     $client
     * @param     $host
     * @param     $port
     * @param int $database
     *
     * @return \Predis\Client|\Redis
     * @throws \WurflCache\Adapter\Exception
     */
    private function buildRedisObject($client, $host, $port, $database = 0)
    {
        if ($client === 'phpredis') {
            $redis = new \Redis();
            $redis->connect($host, $port);
        } elseif ($client === 'predis') {
            $redis = new \Predis\Client(
                array('scheme' => 'tcp', 'host' => $host, 'port' => $port)
            );
            $redis->connect();
        } else {
            throw new Exception('invalid Client given');
        }

        if ($database) {
            $redis->select($database);
        }

        return $redis;
    }

    /**
     * @param string $client
     *
     * @return mixed
     * @throws \WurflCache\Adapter\Exception
     */
    private function checkClient($client)
    {
        if (!in_array($client, array('phpredis', 'predis'))) {
            throw new Exception(
                'Redis client must be phpredis or predis'
            );
        }

        return $client;
    }

    /**
     * @param array $params
     */
    protected function toFields(array $params)
    {
        parent::toFields($params);

        $this->host     = $params['host'];
        $this->port     = $params['port'];
        $this->hashName = $params['hash_name'];
        $this->database = $params['database'];
        $this->client   = $this->checkClient($params['client']);

        if ((null !== $params['redis']) && $this->ensureModuleExistence($params['redis'])) {
            // when using this parameter, the Redis object has to be connected
            // and with the correct database selected
            $this->redis = $params['redis'];
        } else {
            $this->redis = $this->buildRedisObject(
                $this->client,
                $this->host,
                $this->port,
                $this->database
            );
        }
    }
}
