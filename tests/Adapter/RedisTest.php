<?php
/**
 * test case
 */

namespace WurflCacheTest\Adapter;

use WurflCache\Adapter\Helper\StorageObject;
use WurflCache\Adapter\Redis;

/**
 * test case.
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('redis') && !class_exists('\Predis\Client')) {
            self::markTestSkipped('Predis library and Redis extension not present');
        }
    }

    public function testValidPhpRedisInstance()
    {
        if (!extension_loaded('redis')) {
            self::markTestSkipped('Redis extension not present');
        }

        $params          = array();
        $mockRedis       = $this->getMock('\Redis');
        $params['redis'] = $mockRedis;
        $redisStorage    = new Redis($params);
        self::assertInstanceOf('\WurflCache\Adapter\Redis', $redisStorage);
    }

    /**
     * @requires class_exists('\Predis\Client')
     */
    public function testValidPredisInstance()
    {
        if (!class_exists('\Predis\Client')) {
            self::markTestSkipped('Predis library not present');
        }

        $mockRedis       = $this->getMock('\Predis\Client');
        $params['redis'] = $mockRedis;
        $redisStorage    = new Redis($params);
        self::assertInstanceOf('\WurflCache\Adapter\Redis', $redisStorage);
    }

    /**
     * @expectedException \WurflCache\Adapter\Exception
     * @expectedExceptionMessage Connection object is not a Redis or a Predis\Client instance
     */
    public function testInvalidRedisInstance()
    {
        $params          = array();
        $params['redis'] = new \stdClass();
        new Redis($params);
    }

    public function testParametersAreOverridden()
    {
        $params              = array();
        $params['redis']     = $this->getMockRedisObject();
        $params['host']      = '129.0.0.1';
        $params['port']      = '7654';
        $params['database']  = 2;
        $params['hash_name'] = 'WURFL_DATA_TEST';
        $params['client']    = 'predis';

        $redisStorage = new Redis($params);
        self::assertInstanceOf('Redis', $redisStorage);
    }

    /**
     * @expectedException \WurflCache\Adapter\Exception
     * @expectedExceptionMessage Connection object is not a Redis or a Predis\Client instance
     */
    public function testWrongClient()
    {
        $params              = array();
        $params['host']      = '127.0.0.1';
        $params['port']      = '6379';
        $params['database']  = 2;
        $params['hash_name'] = 'WURFL_DATA_TEST';
        $params['client']    = 'FAIL';

        new Redis($params);
    }

    public function testParametersAreLoaded()
    {
        if (class_exists('\Predis\Client')) {
            $params              = array();
            $params['host']      = '127.0.0.1';
            $params['port']      = '6379';
            $params['database']  = 2;
            $params['hash_name'] = 'WURFL_DATA_TEST';
            $params['client']    = 'predis';

            try {
                $redisStorage = new Redis($params);
                self::assertInstanceOf('Redis', $redisStorage);
            } catch (\Predis\Connection\ConnectionException $e) {
                self::markTestIncomplete(
                    'Could not establish connection to Redis using Predis - This test only works' . 'with the standard address of 127.0.0.1:6379 for the Redis server'
                );
            }
        }
        if (class_exists('\Redis')) {
            $params              = array();
            $params['host']      = '127.0.0.1';
            $params['port']      = '6379';
            $params['database']  = 2;
            $params['hash_name'] = 'WURFL_DATA_TEST';
            $params['client']    = 'phpredis';

            try {
                $redisStorage = new Redis($params);
                self::assertInstanceOf('\Redis', $redisStorage);
            } catch (\RedisException $e) {
                self::markTestIncomplete(
                    'Could not establish connection to Redis using phpredis. This test only works' . 'with the standard address of 127.0.0.1:6379 for the Redis server'
                );
            }
        }
    }

    public function testSaveAndLoadObject()
    {
        $value        = new \stdClass();
        $value->value = 1;
        $mockRedis    = $this->getMockRedisObject();
        $mockRedis->expects(self::once())
            ->method('set')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST'),
                $this->equalTo(serialize($value))
            )
            ->willReturn(true);

        $mockRedis->expects(self::once())
            ->method('get')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST')
            )
            ->willReturn(serialize($value));

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new Redis($params);
        self::assertTrue($redisStorage->setItem('TEST', $value), 'Save failed');
        self::assertEquals($value, $redisStorage->getItem('TEST'), 'Save failed');
    }

    public function testSaveAndLoadValue()
    {
        $value     = 1;
        $object    = new StorageObject($value, 0);
        $mockRedis = $this->getMockRedisObject();
        $mockRedis->expects(self::once())
            ->method('set')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST_VALUE_STORAGE_OBJECT'),
                serialize($object)
            )
            ->willReturn(true);

        $mockRedis->expects(self::once())
            ->method('get')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST_VALUE_STORAGE_OBJECT')
            )
            ->willReturn(serialize($object));

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new Redis($params);
        self::assertTrue(
            $redisStorage->setItem('TEST_VALUE_STORAGE_OBJECT', $value),
            'Save failed with StorageObject'
        );
        self::assertEquals(
            $value,
            $redisStorage->getItem('TEST_VALUE_STORAGE_OBJECT'),
            'Load failed with StorageObject'
        );
    }

    public function testClear()
    {
        $mockRedis = $this->getMockRedisObject();
        $mockRedis->expects(self::once())
            ->method('del')
            ->with(
                $this->equalTo('FAKE')
            )
            ->willReturn(true);

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new Redis($params);
        self::assertTrue($redisStorage->flush(), 'Clear failed');
    }

    /**
     * Returns a Predis\Client if predis is present, or Redis object if predis is absent
     * and redis extension is present
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|void
     */
    private function getMockRedisObject()
    {
        if (class_exists('\Predis\Client')) {
            return $this->getMockBuilder('\Predis\Client')
                ->setMethods(array('set', 'get', 'del'))
                ->getMock();
        }

        if (extension_loaded('redis')) {
            return $this->getMockBuilder('\Redis')
                ->setMethods(array('set', 'get', 'del'))
                ->getMock();
        }

        self::markTestSkipped('required Redisclients not loaded');
    }
}
