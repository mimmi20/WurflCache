<?php
namespace WurflCache;

/**
 * Base class for WurflCache Exceptions
 *
 * @category   WurflCache
 * @package    Base
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 * @copyright  2013-2014 Thomas Müller
 * @license    http://www.opensource.org/licenses/MIT MIT License
 * @link       https://github.com/mimmi20/WurflCache/
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
     * @return BrowscapCache
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
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($cacheId, & $success = null)
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
