<?php
namespace WurflCache;

    /**
     * Copyright (c) 2012 ScientiaMobile, Inc.
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU Affero General Public License as
     * published by the Free Software Foundation, either version 3 of the
     * License, or (at your option) any later version.
     * Refer to the COPYING.txt file distributed with this package.
     *
     * @category   WURFL
     * @package    WURFL
     * @copyright  ScientiaMobile, Inc.
     * @license    GNU Affero General Public License
     * @version    $id$
     */
/**
 * Base class for WURFL Exceptions
 *
 * @package    WURFL
 */
class Exception extends \Exception
{
    const LOCAL_FILE_MISSING         = 100;
    const NO_RESULT_CLASS_RETURNED   = 200;
    const STRING_VALUE_EXPECTED      = 300;
    const CACHE_DIR_MISSING          = 400;
    const CACHE_DIR_INVALID          = 401;
    const CACHE_DIR_NOT_READABLE     = 402;
    const CACHE_DIR_NOT_WRITABLE     = 403;
    const CACHE_INCOMPATIBLE         = 500;
    const INVALID_DATETIME           = 600;
    const LOCAL_FILE_NOT_READABLE    = 700;
    const REMOTE_UPDATE_NOT_POSSIBLE = 800;
    const INI_FILE_MISSING           = 900;
}
