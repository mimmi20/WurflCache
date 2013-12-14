<?php
namespace WurflCache;

    /**
     * a outsourced cache class
     *
     * PHP version 5
     *
     * Copyright (c) 2013 Thomas Müller
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
/**
 * Class Browscap
 *
 * @package WurflCache
 */
class BrowscapCache
{
    /**
     * Current version of the class.
     */
    const VERSION = '2.0b';

    /**
     *
     */
    const CACHE_FILE_VERSION = '2.0b';

    /**
     * The update interval in seconds.
     *
     * @var integer
     */
    const UPDATE_INTERVAL = 432000; // 5 days

    /**
     * Path to the cache directory
     *
     * @var null|Adapter\AdapterInterface
     */
    private $cache = null;

    /**
     * Constructor class, checks for the existence of (and loads) the cache and
     * if needed updated the definitions
     *
     * @param Adapter\AdapterInterface $adapter
     *
     * @throws Exception
     */
    public function __construct(Adapter\AdapterInterface $adapter)
    {
        $this->cache = $adapter;
        
        $this->setUpdateInterval(self::UPDATE_INTERVAL);
    }

    /**
     * set the update intervall
     *
     * @param integer $updateInterval
     *
     * @return Browscap
     */
    public function setUpdateInterval($updateInterval)
    {
        $this->cache->setExpiration((int)$updateInterval);

        return $this;
    }

    /**
     * Get an item.
     *
     * @param  string $cacheId
     * @param  bool   $success
     * @param  mixed  $casToken
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($cacheId, & $success = null, & $casToken = null)
    {
        $success = false;
        
        if (!$this->hasItem($cacheId)) {
            return null;
        }
        
        $success = null;
        $data    = $this->cache->getItem($cacheId, $success);

        if (!isset($data['cacheVersion']) || $data['cacheVersion'] != self::CACHE_FILE_VERSION) {
            return null;
        }

        return $data['content'];
    }

    /**
     * save the content into an php file
     *
     * @param string $cacheId The cache id
     * @param mixed  $content The content to store
     *
     * @return boolean whether the file was correctly written to the disk
     */
    public function setItem($cacheId, $content)
    {
        // Get the whole PHP code
        $data = array(
            'cacheVersion' => self::CACHE_FILE_VERSION,
            'content'      => var_export($content, true)
        );

        // Save and return
        return $this->cache->setItem($cacheId, $data);
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
        return $this->cache->hasItem($cacheId);
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
        return $this->cache->removeItem($cacheId);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flush();
    }
}