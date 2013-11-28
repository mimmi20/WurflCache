<?php
namespace WurflCache\Adapter;
/**
 * This software is the Copyright of ScientiaMobile, Inc.
 *
 * Please refer to the LICENSE.txt file distributed with the software for licensing information.
 *
 * @package ScientiaMobile\WurflCloud
 * @subpackage Cache
 */
/**
 * Cookie cache provider
 * @package ScientiaMobile\WurflCloud
 * @subpackage Cache
 */
class Cookie implements AdapterInterface {
	public $cookie_name = 'WurflCloud_Client';
	public $cache_expiration = 86400;
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
        $success = false;

        if (!is_string($key) || !isset($_COOKIE[$key])) {
            return null;
        }

        $cookie_data = @json_decode($_COOKIE[$key], true, 5);

        if (!is_array($cookie_data) || empty($cookie_data)) {
            return null;
        }
        if (!isset($cookie_data['date_set']) || ((int)$cookie_data['date_set'] + $this->cache_expiration) < time()) {
            return null;
        }
        if (!isset($cookie_data['capabilities']) || !is_array($cookie_data['capabilities']) || empty($cookie_data['capabilities'])) {
            return null;
        }

        $success = true;
        return $cookie_data['capabilities'];
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     */
    public function hasItem($key){
        if (!is_string($key) || !isset($_COOKIE[$key])) {
            return false;
        }

        $cookie_data = @json_decode($_COOKIE[$key], true, 5);

        if (!is_array($cookie_data) || empty($cookie_data)) {
            return false;
        }
        if (!isset($cookie_data['date_set']) || ((int)$cookie_data['date_set'] + $this->cache_expiration) < time()) {
            return false;
        }
        if (!isset($cookie_data['capabilities']) || !is_array($cookie_data['capabilities']) || empty($cookie_data['capabilities'])) {
            return false;
        }

        return true;
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
        $cookie_data = array(
            'date_set' => time(),
            'capabilities' => $value,
        );

        return setcookie($key, json_encode($cookie_data, JSON_FORCE_OBJECT), $cookie_data['date_set'] + $this->cache_expiration);
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
        return setcookie($key, '', time() - 3600);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return null;
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
}