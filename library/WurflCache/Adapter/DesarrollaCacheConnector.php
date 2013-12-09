<?php
namespace WurflCache\Adapter;

use Desarrolla2\Cache\CacheInterface as DesarrollaInterface;

/**
 * Interface class to use the Desarrolla2 cache with Browscap
 *
 * PHP version 5
 *
 * Copyright (c) 2006-2012 Jonathan Stoppani
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
 * @package    Browscap
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 * @copyright  Copyright (c) 2013 Thomas Müller
 * @version    1.0
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/phpbrowscap/
 */
class DesarrollaCacheConnector extends AbstractAdapter implements AdapterInterface
{
    /**
     * a Desarrolla2 Cache instance
     *
     * @var \Desarrolla2\Cache\CacheInterface
     */
    private $cache = null;

    /**
     * Constructor class, checks for the existence of (and loads) the cache and
     * if needed updated the definitions
     *
     * @param \Desarrolla2\Cache\CacheInterface $cache
     *
     * @throws Exception
     */
    public function __construct(DesarrollaInterface $cache)
    {
        $this->cache = $cache;
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
        return $this->cache->get($key);
    }

    /**
     * Store an item.
     *
     * @param string $cacheId
     * @param mixed  $content
     *
     * @return bool
     */
    public function setItem($cacheId, $content)
    {
        return $this->cache->set($cacheId, $content);
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
        return $this->cache->has($cacheId);
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
        return $this->cache->delete($key);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->dropCache();
    }
}