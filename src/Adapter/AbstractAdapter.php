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
 * @package    Adapter
 * @copyright  2013-2014 Thomas Müller
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/WurflCache/
 */

namespace WurflCache\Adapter;

/**
 * Base class for all Adapters
 *
 * @category   WurflCache
 * @package    Adapter
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 * @copyright  2013-2014 Thomas Müller
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/WurflCache/
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * the time until the cache expires
     *
     * @var integer
     */
    protected $cacheExpiration = 86400;

    /**
     * the namespace used to build the internal cache id
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * @var int
     */
    protected $expiration = 0;

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
        $success = false;
        return null;
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
        return true;
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
        return false;
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
        return true;
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return true;
    }

    /**
     * set the cacheExpiration time
     *
     * @param int $expiration
     *
     * @return AdapterInterface
     */
    public function setExpiration($expiration = 86400)
    {
        $this->cacheExpiration = (int) $expiration;

        return $this;
    }

    /**
     * set the cache namespace
     *
     * @param string $namespace
     *
     * @return AdapterInterface
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * normalizes the cache id for the cache
     *
     * @param string $cacheId The cache id
     *
     * @return string The formated cache id
     */
    protected function normalizeKey($cacheId)
    {
        return Helper\IdGenerator::encode($this->namespace, $cacheId);
    }

    /**
     * compacts the content for the cache
     *
     * @param mixed $content
     *
     * @return string
     */
    protected function compact($content)
    {
        /** @var $object Helper\StorageObject */
        $object = new Helper\StorageObject($content, $this->cacheExpiration);
        return serialize($object);
    }

    /**
     * compacts the content for the cache
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function extract($value)
    {
        /** @var $object Helper\StorageObject */
        $object = unserialize($value);
        if ($value === $object) {
            return null;
        }

        if (!($object instanceof Helper\StorageObject)) {
            return null;
        }

        if ($object->isExpired()) {
            return null;
        }

        return $object->value();
    }
}
