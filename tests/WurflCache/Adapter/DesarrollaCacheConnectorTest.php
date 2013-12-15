<?php
namespace WurflCacheTest\Adapter;

use WurflCache\Adapter\DesarrollaCacheConnector;

/**
 * Interface class to use the Desarrolla2 cache with Browscap
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
class DesarrollaCacheConnectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testGetItemError()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache');
        $object = new DesarrollaCacheConnector($mock);
        $object->getItem();
    }

    /**
     * Get an item.
     */
    public function testGetItemNull()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('get'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('get')
            ->will(self::returnValue(null))
        ;

        $object = new DesarrollaCacheConnector($mock);
        self::assertNull($object->getItem('test'));
    }

    /**
     * Get an item.
     */
    public function testGetItemMocked()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('get'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('get')
            ->will(self::returnValue('test'))
        ;

        $object = new DesarrollaCacheConnector($mock);
        self::assertSame('test', $object->getItem('test'));
    }

    /**
     * Test if an item exists.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testHasItemError()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache');
        $object = new DesarrollaCacheConnector($mock);
        $object->hasItem();
    }

    /**
     * Test if an item exists.
     */
    public function testHasItem()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('has'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('has')
            ->will(self::returnValue(false))
        ;
        $object = new DesarrollaCacheConnector($mock);
        self::assertFalse($object->hasItem('test'));
    }

    /**
     * Store an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testSetItemError1()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache');
        $object = new DesarrollaCacheConnector($mock);
        $object->setItem();
    }

    /**
     * Store an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testSetItemError2()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache');
        $object = new DesarrollaCacheConnector($mock);
        $object->setItem('test');
    }

    /**
     * Store an item.
     */
    public function testSetItem()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('set'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('set')
            ->will(self::returnValue(true))
        ;
        $object = new DesarrollaCacheConnector($mock);
        self::assertTrue($object->setItem('test', 'testValue'));
    }

    /**
     * Remove an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testRemoveItemError()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache');
        $object = new DesarrollaCacheConnector($mock);
        $object->removeItem();
    }

    /**
     * Store an item.
     */
    public function testRemoveItem()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('delete'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('delete')
            ->will(self::returnValue(true))
        ;
        $object = new DesarrollaCacheConnector($mock);
        self::assertTrue($object->removeItem('test'));
    }

    /**
     * Flush the whole storage
     */
    public function testflush()
    {
        $mock = $this->getMock('\\Desarrolla2\\Cache\\Cache', array('dropCache'), array(), '', false);
        $mock
            ->expects(self::once())
            ->method('dropCache')
            ->will(self::returnValue(false))
        ;
        $object = new DesarrollaCacheConnector($mock);
        self::assertFalse($object->flush());
    }
}
