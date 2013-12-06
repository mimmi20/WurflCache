<?php
namespace WurflCache\Adapter;

/**
 * the cacxhe interface for the browscap project
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
 * Interface AdapterInterface
 *
 * @package WurflCache\Adapter
 */
interface AdapterInterface
{
    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool   $success
     * @param  mixed  $casToken
     *
     * @return mixed Data on success, null on failure
     */
    public function getItem($key, & $success = null, & $casToken = null);

    /**
     * Test if an item exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function hasItem($key);

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return bool
     */
    public function setItem($key, $value);

    /**
     * Remove an item.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function removeItem($key);

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush();

    /**
     * set the expiration time
     *
     * @param integer $expiration
     *
     * @return AdapterInterface
     */
    public function setExpiration($expiration = 86400);

    /**
     * set the cache namespace
     *
     * @param string $namespace
     *
     * @return AdapterInterface
     */
    public function setNamespace($namespace);
}
