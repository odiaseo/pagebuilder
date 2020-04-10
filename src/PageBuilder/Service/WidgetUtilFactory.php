<?php
namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use PageBuilder\Util\Widget;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class WidgetUtilFactory
 *
 * @package PageBuilder\Service
 */
class WidgetUtilFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     *
     * @return Widget
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $widget = new Widget();
        $widget->setServiceManager($serviceLocator);
        $widget->init();

        return $widget;
    }
}
