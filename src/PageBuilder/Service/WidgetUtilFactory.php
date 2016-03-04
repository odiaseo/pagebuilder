<?php
namespace PageBuilder\Service;

use PageBuilder\Util\Widget;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class WidgetUtilFactory
 * @package PageBuilder\Service
 */
class WidgetUtilFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $widget = new Widget();
        $widget->setServiceManager($serviceLocator);
        $widget->init();

        return $widget;
    }
}
