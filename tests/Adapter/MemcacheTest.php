<?php
namespace WurflCacheTest\Adapter;

use WurflCache\Adapter\Memcache;

/**
 * test case
 */

/**
 * test case.
 */
class MemcacheTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped(
                'PHP extension \'memcache\' must be loaded and a local memcache server running to run this test.'
            );
        }
    }

    public function testMultipleServerConfiguration()
    {
        $params = array(
            'host' => '127.0.0.1;127.0.0.2'
        );

        new Memcache($params);
    }

    /**
     * @covers \WurflCache\Adapter\Memcache::setItem
     * @covers \WurflCache\Adapter\Memcache::getItem
     */
    public function testNeverToExpireItems()
    {
        $storage = new Memcache();
        $storage->setItem('foo', 'foo');
        sleep(2);
        self::assertEquals('foo', $storage->getItem('foo'));
    }

    /**
     * @covers \WurflCache\Adapter\Memcache::setItem
     * @covers \WurflCache\Adapter\Memcache::getItem
     */
    public function testShouldRemoveTheExpiredItem()
    {
        $params  = array('expiration' => 1);
        $storage = new Memcache($params);
        $storage->setItem('key', 'value');
        sleep(2);
        self::assertEquals(null, $storage->getItem('key'));
    }

    /**
     * @covers \WurflCache\Adapter\Memcache::setItem
     * @covers \WurflCache\Adapter\Memcache::getItem
     * @covers \WurflCache\Adapter\Memcache::flush
     */
    public function testShouldClearAllItems()
    {
        $storage = new Memcache(array());
        $storage->setItem('key1', 'item1');
        $storage->setItem('key2', 'item2');
        $storage->flush();
        $this->assertThanNoElementsAreInStorage(array('key1', 'key2'), $storage);
    }

    /**
     * @param array    $keys
     * @param Memcache $storage
     */
    private function assertThanNoElementsAreInStorage(array $keys = array(), Memcache $storage = null)
    {
        foreach ($keys as $key) {
            self::assertNull($storage->getItem($key));
        }
    }
}
