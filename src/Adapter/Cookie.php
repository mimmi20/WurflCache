<?php
namespace WurflCache\Adapter;

    /**
     * This software is the Copyright of ScientiaMobile, Inc.
     *
     * Please refer to the LICENSE.txt file distributed with the software for licensing information.
     *
     * @package    ScientiaMobile\WurflCloud
     * @subpackage Cache
     */
/**
 * Class Cookie
 *
 * @package WurflCache\Adapter
 */
class Cookie extends AbstractAdapter
{

    /**
     * Get an item.
     *
     * @param  string $cacheId
     * @param  bool   $success
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($cacheId, & $success = null)
    {
        $success = false;
        $cacheId = $this->normalizeKey($cacheId);

        if (!isset($_COOKIE[$cacheId])) {
            return null;
        }

        $value = $this->extract($_COOKIE[$cacheId]);
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
        $success = false;
        $cacheId = $this->normalizeKey($cacheId);

        $this->getItem($cacheId, $success);

        return $success;
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

        return setcookie($cacheId, $this->compact($value), time() + $this->cacheExpiration);
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
        $cacheId = $this->normalizeKey($cacheId);

        return setcookie($cacheId, '', time() - 3600);
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        return false;
    }
}
