<?php

namespace PageBuilderTest\Service;

use SynergyCommonTest\Bootstrap;
use Zend\Cache\Storage\StorageInterface;

/**
 * Class QueryCacheProfileTest
 * @package SynergyCommonTest
 */
class CacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testRedisCacheAdapter()
    {
        $adapter = Bootstrap::getServiceManager()->get('cache\adapter\redis');
        $this->assertInstanceOf(StorageInterface::class, $adapter);
    }
}
