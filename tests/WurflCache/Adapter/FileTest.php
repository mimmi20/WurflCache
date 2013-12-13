<?php
/**
 * test case
 */
/**
 * test case.
 */
class WURFL_Storage_FileTest extends PHPUnit_Framework_TestCase
{

    const STORAGE_DIR = "../../../resources/storage";

    public function setUp()
    {
        \WurflCache\Utils\FileUtils::mkdir(self::storageDir());
    }

    public function tearDown()
    {
        \WurflCache\Utils\FileUtils::rmdir(self::storageDir());
    }

    public function testShouldTryToCreateTheStorage()
    {
        $cachepath = $this->realpath(self::STORAGE_DIR . "/cache");
        $params    = array(
            "dir" => $cachepath
        );
        new \WurflCache\Adapter\File($params);
        $this->assertStorageDirectoryIsCreated($cachepath);
        \WurflCache\Utils\FileUtils::rmdir($cachepath);
    }

    private function realpath($path)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . $path;
    }

    private function assertStorageDirectoryIsCreated($dir)
    {
        self::assertTrue(file_exists($dir) && is_writable($dir));
    }

    public function testNeverToExpireItems()
    {
        $params = array(
            "dir"        => self::storageDir(),
            "expiration" => 0
        );

        $storage = new \WurflCache\Adapter\File($params);

        $storage->setItem("foo", "foo");
        sleep(1);
        self::assertEquals("foo", $storage->getItem("foo"));
    }

    public function testShouldRemoveTheExpiredItem()
    {

        $params = array(
            "dir"        => self::storageDir(),
            "expiration" => 1
        );

        $storage = new \WurflCache\Adapter\File($params);

        $storage->setItem("item2", "item2");
        self::assertEquals("item2", $storage->getItem("item2"));
        sleep(2);
        self::assertEquals(null, $storage->getItem("item2"));
    }

    public static function storageDir()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . self::STORAGE_DIR;
    }
}
