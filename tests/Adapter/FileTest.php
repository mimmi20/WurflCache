<?php
namespace WurflCacheTest\Adapter;

/**
 * test case
 */
use WurflCache\Adapter\File;
use org\bovigo\vfs\vfsStream;

/**
 * test case.
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    const STORAGE_DIR = 'storage';

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root = null;

    public function setUp()
    {
        $this->root = vfsStream::setup(self::STORAGE_DIR);
    }

    public function testShouldTryToCreateTheStorage()
    {
        $params = array(
            'dir' => vfsStream::url(self::STORAGE_DIR)
        );

        new File($params);

        $dir = vfsStream::url(self::STORAGE_DIR);

        self::assertTrue(file_exists($dir));
        self::assertTrue(is_writable($dir));
    }

    public function testShouldTryToCreateTheReadonlyStorage()
    {
        $params = array(
            'dir'      => vfsStream::url(self::STORAGE_DIR . DIRECTORY_SEPARATOR . 'test'),
            'readonly' => true
        );

        new File($params);

        $dir = vfsStream::url(self::STORAGE_DIR);

        self::assertTrue(file_exists($dir));
        self::assertTrue(is_writable($dir));
    }

    public function testNotFoundItems()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 0
        );

        $storage = new File($params);

        self::assertNull($storage->getItem('foo'));
    }

    public function testNeverToExpireItems()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 0
        );

        $storage = new File($params);

        self::assertTrue($storage->setItem('foo', 'foo'), 'Could not write "foo" to Item "foo"');
        sleep(1);
        self::assertEquals('foo', $storage->getItem('foo'));
    }

    public function testShouldRemoveTheExpiredItem()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 1
        );

        $storage = new File($params);

        $storage->setItem('item2', 'item2');
        self::assertEquals('item2', $storage->getItem('item2'));
        sleep(2);
        self::assertEquals(null, $storage->getItem('item2'));
    }

    /**
     * Flush the whole storage
     */
    public function testflush()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 0
        );

        $storage = new File($params);

        $storage->setItem('foo', 'foo');
        $storage->setItem('bar', 'bar');
        $storage->setItem('foobar', 'foobar');

        self::assertTrue($storage->flush());
    }

    /**
     * Store an item.
     */
    public function testRemoveItemException()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 0
        );

        $storage = new File($params);

        $storage->setItem('foo', 'foo');

        self::assertTrue($storage->removeItem('foo'));
    }
}
