<?php
namespace WurflCacheTest\Adapter;

/**
 * test case
 */
use WurflCache\Adapter\Apc;

/**
 * test case.
 */
class ApcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \WurflCache\Adapter\Memory
     */
    private $object = null;

    public function setUp()
    {
        if (!extension_loaded('apc')) {
            self::markTestSkipped('PHP must have APC support.');
        }

        $this->object = new Apc();
    }

    /**
     * Get an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testGetItemError()
    {
        $this->object->getItem();
    }

    /**
     * Get an item.
     */
    public function testGetItemNull()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Get an item.
     */
    public function testGetItemMocked()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Test if an item exists.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testHasItemError()
    {
        $this->object->hasItem();
    }

    /**
     * Test if an item exists.
     */
    public function testHasItem()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Store an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testSetItemError1()
    {
        $this->object->setItem();
    }

    /**
     * Store an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testSetItemError2()
    {
        $this->object->setItem('test');
    }

    /**
     * Store an item.
     */
    public function testSetItem()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Store an item.
     */
    public function testSetGetItem()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Remove an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testRemoveItemError()
    {
        $this->object->removeItem();
    }

    /**
     * Store an item.
     */
    public function testRemoveItem()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Flush the whole storage
     */
    public function testflush()
    {
        self::markTestIncomplete('need to implement');
    }

    /**
     * Store an item.
     *
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testSetNamespaceError()
    {
        $this->object->setNamespace();
    }

    /**
     * Store an item.
     */
    public function testSetNamespace()
    {
        self::assertSame($this->object, $this->object->setNamespace('test'));
    }

    /**
     * Store an item.
     */
    public function testSetExpiration()
    {
        self::assertSame($this->object, $this->object->setExpiration('test'));
    }
}
