<?php
namespace WurflCacheTest\Adapter\Helper;

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
use WurflCache\Adapter\Helper\IdGenerator;

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
class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \WurflCache\Adapter\Helper\IdGenerator
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $this->object = new IdGenerator();
    }

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testEncodeError1()
    {
        $this->object->encode();
    }

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testEncodeError2()
    {
        self::assertTrue($this->object->encode('test'));
    }

    /**
     * Encode the Object Id using the Persistence Identifier
     *
     *
     */
    public function testEncode()
    {
        self::assertSame('4ed727fa6a2dcc72e95e08559129b2cc6ca9bf7210052c4d832c7b6f4250715113cc5ef99adeb19234545febf150b9b9bdd069a16c902179e74b97fecff02018', $this->object->encode('test', 'testValue'));
    }
}