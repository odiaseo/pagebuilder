<?php
namespace PageBuilder\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class RedisCacheAdapterFactory
 * @package SynergyCommon\Service\Factory
 */
class RedisCacheAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $site \PageBuilder\Entity\Site */
        $site    = $serviceLocator->get('active\site');
        $config  = $serviceLocator->get('config');
        $options = $config['caches']['redis'];
        $prefix  = $site ? $site->getSessionNamespace() : 'zfcache';

        $options['adapter']['options']['namespace'] = 'redis' . $prefix;

        return StorageFactory::factory($options);
    }
}
