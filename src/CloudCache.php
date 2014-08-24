<?php
/**
 * Copyright (c) 2013-2014 Thomas Müller
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   WurflCache
 * @package    Base
 * @copyright  2013-2014 Thomas Müller
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/WurflCache/
 */

namespace WurflCache;

/**
 * Class to use with the Wurfl Cloud
 *
 * @category   WurflCache
 * @package    Base
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 * @copyright  2013-2014 Thomas Müller
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/WurflCache/
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

        if (!$success) {
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

        if (!$success) {
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
