<?php
namespace PageBuilder\Service;

use PageBuilder\WidgetInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class WidgetInitializers
 * @package PageBuilder\Service
 */
class WidgetInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $widget
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($widget, ServiceLocatorInterface $serviceLocator)
    {
        /** @var $widget  \object */
        /** @var $mvcEvent \Zend\Mvc\MvcEvent */
        if ($widget instanceof WidgetInterface) {
            $mvcEvent = $serviceLocator->get('application')->getMvcEvent();
            $widget->setServiceManager($serviceLocator);
            $widget->setView($serviceLocator->get('viewrenderer'));
            $widget->setMvcEvent($mvcEvent);
            $response = $widget->init();

            //send response if we have a widget returning a response instance
            if ($response instanceof Response) {
                $mvcEvent->stopPropagation();

                return false;
            }
        }

        return true;
    }
}
