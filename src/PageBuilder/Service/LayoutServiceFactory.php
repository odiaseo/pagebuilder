<?php
namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class WidgetUtilFactory
 *
 * @package PageBuilder\Service
 */
class LayoutServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     *
     * @return LayoutService
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new LayoutService($serviceLocator);
    }
}
