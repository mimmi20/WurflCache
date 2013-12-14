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
     * @param string $userAgent
     *
     * @return array|bool
     */
    public function getDevice($userAgent)
    {
        $success = null;
        $data    = $this->cache->getItem($userAgent, $success);

        if (!$success)
            return false;
        }

        return $data;
    }

    /**
     * @param string $deviceId
     *
     * @return array|bool
     */
    public function getDeviceFromID($deviceId)
    {
        $success = null;
        $data    = $this->cache->getItem($deviceId, $success);

        if (!$success)
            return false;
        }

        return $data;
    }

    /**
     * @param string $userAgent
     * @param array  $capabilities
     *
     * @return bool
     */
    public function setDevice($userAgent, array $capabilities)
    {
        return $this->cache->setItem($userAgent, $capabilities);
    }

    // Required by interface but not used for this provider
    /**
     * @param string $deviceId
     * @param array  $capabilities
     *
     * @return bool
     */
    public function setDeviceFromID($deviceId, array $capabilities)
    {
        return $this->cache->setItem($deviceId, $capabilities);
    }

    /**
     * @param int $time
     */
    public function setCacheExpiration($time)
    {
        $this->cache->setExpiration($time);
    }

    /**
     * @param string $prefix
     */
    public function setCachePrefix($prefix)
    {
        $this->cache->setNamespace($prefix);
    }

    /**
     *
     */
    public function close()
    {
    }
}
