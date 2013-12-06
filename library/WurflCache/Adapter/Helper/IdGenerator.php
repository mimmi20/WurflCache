<?php
namespace WurflCache\Adapter\Helper;

    /**
     * Copyright (c) 2012 ScientiaMobile, Inc.
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU Affero General Public License as
     * published by the Free Software Foundation, either version 3 of the
     * License, or (at your option) any later version.
     * Refer to the COPYING.txt file distributed with this package.
     *
     * @category   WURFL
     * @package    \Wurfl\Storage
     * @copyright  ScientiaMobile, Inc.
     * @license    GNU Affero General Public License
     * @version    $id$
     */

/**
 * Base Storage Provider
 * A Skeleton implementation of the Storage Interface
 *
 * @category   WURFL
 * @package    \Wurfl\Storage
 * @copyright  ScientiaMobile, Inc.
 * @license    GNU Affero General Public License
 * @author     Fantayeneh Asres Gizaw
 * @version    $id$
 */
class IdGenerator
{

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     * @param string $namespace
     * @param        $cacheId
     *
     * @internal param string $input
     *
     * @return string $input with the given $namespace as a prefix
     */
    public static function encode($namespace, $cacheId)
    {
        $cacheId = implode(':', array('Wurfl', $namespace, $cacheId));

        return hash('sha512', $cacheId);
    }
}