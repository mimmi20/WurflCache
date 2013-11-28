<?php
namespace WurflCache\Adapter;

use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\Plugin\Serializer;

/**
 * Interface class to use the zend cache with Browscap
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
class ZendCacheConnector implements AdapterInterface
{
    /**
     * a Zend Cache instance
     *
     * @var AbstractAdapter
     */
    protected $cache = null;

    /**
     * Constructor class, checks for the existence of (and loads) the cache and
     * if needed updated the definitions
     *
     * @param AbstractAdapter $cache
     */
    public function __construct(AbstractAdapter $cache)
    {
        $this->cache = $cache;
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
        $cacheId = $this->normalizeKey($key);

        try {
            $content = $this->cache->getItem($cacheId, $success);
        } catch (Exception $ex) {
            $success = false;
            return null;
        }

        if (!$this->cache->hasPlugin(new Serializer())) {
            $content = unserialize($content);
        }

        return $content;
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
        $cacheId = $this->normalizeKey($cacheId);

        if (!$this->cache->hasPlugin(new Serializer())) {
            $content = serialize($content);
        }

        try {
            return $this->cache->setItem($cacheId, $content);
        } catch (Exception $ex) {
            return null;
        }
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

        try {
            return $this->cache->hasItem($cacheId);
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * normalizes the cache id for zend cache
     *
     * @param string $cacheId The cache id
     *
     * @return string The formated cache id
     */
    private function normalizeKey($cacheId)
    {
        if (($p = $this->cache->getOptions()->getKeyPattern()) && !preg_match($p, $cacheId)) {
            $p = str_replace(array('^[', '*$'), array('[^', ''), $p);

            $cacheId = preg_replace($p, '_', $cacheId);
        }

        $cacheId = hash('sha512', $cacheId);

        return $cacheId;
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
        return null;
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