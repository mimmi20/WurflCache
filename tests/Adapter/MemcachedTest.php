<?php
namespace WurflCacheTest\Adapter;

use WurflCache\Adapter\Memcached;

/**
 * test case
 */

/**
 * test case.
 */
class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped(
                'PHP extension \'Memcached\' must be loaded and a local Memcached server running to run this test.'
            );
        }
    }

    public function testMultipleServerConfiguration()
    {
        $params = array(
            'host' => '127.0.0.1;127.0.0.2'
        );

        new Memcached($params);
    }

    /**
     * @covers \WurflCache\Adapter\Memcached::setItem
     * @covers \WurflCache\Adapter\Memcached::getItem
     */
    public function testNeverToExpireItems()
    {
        $storage = new Memcached();
        $storage->setItem('foo', 'foo');
        sleep(2);
        self::assertEquals('foo', $storage->getItem('foo'));
    }

    /**
     * @covers \WurflCache\Adapter\Memcached::setItem
     * @covers \WurflCache\Adapter\Memcached::getItem
     */
    public function testShouldRemoveTheExpiredItem()
    {
        $params  = array('expiration' => 1);
        $storage = new Memcached($params);
        $storage->setItem('key', 'value');
        sleep(2);
        self::assertEquals(null, $storage->getItem('key'));
    }

    /**
     * @covers \WurflCache\Adapter\Memcached::setItem
     * @covers \WurflCache\Adapter\Memcached::getItem
     * @covers \WurflCache\Adapter\Memcached::flush
     */
    public function testShouldClearAllItems()
    {
        $storage = new Memcached(array());
        $storage->setItem('key1', 'item1');
        $storage->setItem('key2', 'item2');
        $storage->flush();
        $this->assertThanNoElementsAreInStorage(array('key1', 'key2'), $storage);
    }

    /**
     * @param array    $keys
     * @param Memcached $storage
     */
    private function assertThanNoElementsAreInStorage(array $keys = array(), Memcached $storage = null)
    {
        foreach ($keys as $key) {
            self::assertNull($storage->getItem($key));
        }
    }

    /**
     * Get an item.
     */
    public function testGetItemNull()
    {
        self::assertNull($this->object->getItem('test'));
    }

    /**
     * Get an item.
     */
    public function testGetItemMocked()
    {
        /** @var $object \WurflCache\Adapter\Memory */
        $object = $this->getMock('\\WurflCache\\Adapter\\Memory', array('normalizeKey'));

        self::assertNull($object->getItem('test'));
    }

    /**
     * Test if an item exists.
     */
    public function testHasItem()
    {
        self::assertFalse($this->object->hasItem('test'));
    }

    /**
     * Store an item.
     */
    public function testSetItem()
    {
        self::assertTrue($this->object->setItem('test', 'testValue'));
    }

    /**
     * Store an item.
     */
    public function testSetGetItem()
    {
        $cacheId    = 'test';
        $cacheValue = 'testValue';

        self::assertTrue($this->object->setItem($cacheId, $cacheValue));

        $success = null;
        self::assertSame($cacheValue, $this->object->getItem($cacheId, $success));
    }

    /**
     * Store an item.
     */
    public function testRemoveItem()
    {
        self::assertTrue($this->object->removeItem('test'));
    }

    /**
     * Flush the whole storage
     */
    public function testflush()
    {
        self::assertTrue($this->object->flush());
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
