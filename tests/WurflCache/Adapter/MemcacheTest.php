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

    public function testMultipleServerConfiguration()
    {
        $params = array(
            'host' => '127.0.0.1;127.0.0.2'
        );
        $this->checkDeps();
        new Memcache($params);
    }

    /**
     * @covers Memcache::save
     * @covers Memcache::load
     */
    public function testNeverToExpireItems()
    {
        $this->checkDeps();
        $storage = new Memcache();
        $storage->setItem('foo', 'foo');
        sleep(2);
        self::assertEquals('foo', $storage->getItem('foo'));
    }

    /**
     * @covers Memcache::save
     * @covers Memcache::load
     */
    public function testShouldRemoveTheExpiredItem()
    {
        $this->checkDeps();
        $params  = array('expiration' => 1);
        $storage = new Memcache($params);
        $storage->setItem('key', 'value');
        sleep(2);
        self::assertEquals(null, $storage->getItem('key'));
    }

    /**
     * @covers Memcache::save
     * @covers Memcache::load
     * @covers Memcache::clear
     */
    public function testShouldClearAllItems()
    {
        $this->checkDeps();
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

    private function checkDeps()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped(
                'PHP extension \'memcache\' must be loaded and a local memcache server running to run this test.'
            );
        }
    }
}
