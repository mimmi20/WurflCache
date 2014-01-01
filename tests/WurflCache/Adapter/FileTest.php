<?php
namespace WurflCacheTest\Adapter;

/**
 * test case
 */
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use WurflCache\Adapter\File;
use WurflCache\Utils\FileUtils;
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
    {//var_dump(stream_get_wrappers(), vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure());
        $params    = array(
            'dir' => vfsStream::url(self::STORAGE_DIR)
        );

        new File($params);

        $this->assertStorageDirectoryIsCreated(vfsStream::url(self::STORAGE_DIR));
    }

    private function assertStorageDirectoryIsCreated($dir)
    {
        self::assertTrue(file_exists($dir) && is_writable($dir));
    }

    public function testNeverToExpireItems()
    {
        $params = array(
            'dir'        => vfsStream::url(self::STORAGE_DIR),
            'expiration' => 0
        );

        $storage = new File($params);

        $storage->setItem('foo', 'foo');
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
}
