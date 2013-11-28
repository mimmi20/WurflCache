<?php
namespace WurflCache\Adapter;

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
class Browscap implements AdapterInterface
{
    /**
     * Current version of the class.
     */
    const VERSION = '2.0b';

    const CACHE_FILE_VERSION = '2.0b';

    /**
     * The update interval in seconds.
     *
     * @var integer
     */
    private $updateInterval = 432000;  // 5 days

    /**
     * Path to the cache directory
     *
     * @var string
     */
    private $cacheDir = null;

    /**
     * Constructor class, checks for the existence of (and loads) the cache and
     * if needed updated the definitions
     *
     * @param string $cacheDir
     * @throws Exception
     */
    public function __construct($cacheDir)
    {
        if (!isset($cacheDir)) {
            throw new Exception(
                'You have to provide a path to read/store the cache file'
            );
        }

        $oldCacheDir = $cacheDir;
        $cacheDir    = realpath($cacheDir);

        if (false === $cacheDir) {
            throw new Exception(
                sprintf('The cache path %s is invalid. Are you sure that it exists and that you have permission to access it?', $oldCacheDir)
            );
        }

        // Is the cache dir really the directory or is it directly the file?
        if (substr($cacheDir, -4) === '.php') {
            $this->cacheDir = dirname($cacheDir);
        } else {
            $this->cacheDir = $cacheDir;
        }

        $this->cacheDir .= DIRECTORY_SEPARATOR;
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
        $this->updateInterval = (int) $updateInterval;

        return $this;
    }

    /**
     * loads the content from the cache
     *
     * @param string  $cacheId  The cache id
     * @param boolean &$success A flag to tell if the cache was loaded
     *
     * @return mixed the content that was saved before
     */
    public function getItem($cacheId, &$success)
    {
        $cacheFile = $this->getCacheFile($cacheId);
        $content   = null;
        $success   = false;

        if (!file_exists($cacheFile) || !is_readable($cacheFile) || !is_file($cacheFile)) {
            return null;
        }

        $interval = time() - filemtime($cacheFile);

        if (file_exists($cacheFile) && ($interval <= $this->updateInterval)) {
            $content = $this->_loadCache($cacheFile);
            $success = true;
        }

        return $content;
    }

    /**
     * Loads the cache into object's properties
     *
     * @param string $cacheFile
     *
     * @return mixed
     */
    private function _loadCache($cacheFile)
    {
        $content = null;
        require $cacheFile;

        if (!isset($cache_version) || $cache_version != self::CACHE_FILE_VERSION) {
            return null;
        }

        return $content;
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
        $cacheFile = $this->getCacheFile($cacheId);

        // Get the whole PHP code
        $cache = $this->_buildCache($content);

        // Save and return
        return (bool) file_put_contents($cacheFile, $cache, LOCK_EX);
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
        $cacheFile = $this->getCacheFile($cacheId);

        return file_exists($cacheFile)
            && is_file($cacheFile)
            && is_readable($cacheFile);
    }

    /**
     * creates the PHP string to write to disk
     *
     * @param mixed $content The content to store
     *
     * @return string the PHP string to save into the cache file
     */
    private function _buildCache($content)
    {
        $cacheTpl = "<?php\n\$cache_version=%s;\n\$content=%s;\n";

        return sprintf(
            $cacheTpl,
            "'" . self::CACHE_FILE_VERSION . "'",
            var_export($content, true)
        );
    }

    /**
     * builds the full path for the cache file
     *
     * @param string $cacheId The cache id
     *
     * @return string The cache file including path
     */
    private function getCacheFile($cacheId)
    {
        $cacheId = preg_replace('/[^a-zA-Z0-9_]/', '_', $cacheId);
        $cacheId = hash('sha512', $cacheId);

        return $this->cacheDir . $cacheId . '.php';
    }
}