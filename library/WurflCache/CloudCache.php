<?php
namespace WurflCache;

    /**
     * This software is the Copyright of ScientiaMobile, Inc.
     *
     * Please refer to the LICENSE.txt file distributed with the software for licensing information.
     *
     * @package    ScientiaMobile\WurflCloud
     * @subpackage Cache
     */
/**
 * Class CloudCache
 *
 * @package WurflCache
 */
class CloudCache implements CacheInterface
{
    /**
     * @var string
     */
    private $cookie_name = 'WurflCloud_Client';
    /**
     * @var int
     */
    private $cache_expiration = 86400;
    /**
     * @var null|Adapter\AdapterInterface
     */
    private $cache = null;

    /**
     * @param Adapter\AdapterInterface $adapter
     */
    public function __construct(Adapter\AdapterInterface $adapter)
    {
        $this->cache = $adapter;
    }

    /**
     * @param string $user_agent
     *
     * @return array|bool
     */
    public function getDevice($user_agent)
    {
        if (!isset($_COOKIE[$this->cookie_name])) {
            return false;
        }
        $cookie_data = @json_decode($_COOKIE[$this->cookie_name], true, 5);
        if (!is_array($cookie_data) || empty($cookie_data)) {
            return false;
        }
        if (!isset($cookie_data['date_set']) || ((int)$cookie_data['date_set'] + $this->cache_expiration) < time()) {
            return false;
        }
        if (!isset($cookie_data['capabilities']) || !is_array($cookie_data['capabilities'])
            || empty($cookie_data['capabilities'])
        ) {
            return false;
        }
        return $cookie_data['capabilities'];
    }

    /**
     * @param string $device_id
     *
     * @return bool
     */
    public function getDeviceFromID($device_id)
    {
        return false;
    }

    /**
     * @param string $user_agent
     * @param array  $capabilities
     *
     * @return bool
     */
    public function setDevice($user_agent, $capabilities)
    {
        if ($this->cookie_sent === true) {
            return true;
        }

        $cookie_data = array(
            'date_set'     => time(),
            'capabilities' => $capabilities,
        );
        $this->setCookie(
            $this->cookie_name, json_encode($cookie_data, JSON_FORCE_OBJECT),
            $cookie_data['date_set'] + $this->cache_expiration
        );
        $this->cookie_sent = true;

        return true;
    }

    // Required by interface but not used for this provider
    /**
     * @param string $device_id
     * @param array  $capabilities
     *
     * @return bool
     */
    public function setDeviceFromID($device_id, $capabilities)
    {
        return true;
    }

    /**
     * @param int $time
     */
    public function setCacheExpiration($time)
    {
        $this->cache_expiration = $time;
    }

    /**
     * @param string $prefix
     */
    public function setCachePrefix($prefix)
    {
    }

    /**
     *
     */
    public function close()
    {
    }

    /**
     * @param      $name
     * @param null $value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httponly
     *
     * @return bool
     */
    protected function setCookie(
        $name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null
    ) {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}