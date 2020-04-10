<?php
namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use PageBuilder\BaseWidget;
use PageBuilder\WidgetInterface;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\ServiceManager\Initializer\InitializerInterface;

/**
 * Class WidgetInitializers
 *
 * @package PageBuilder\Service
 */
class WidgetInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param object $widget
     *
     * @return bool
     */
    public function __invoke(ContainerInterface $serviceLocator, $widget)
    {
        /** @var $widget  BaseWidget */
        /** @var $mvcEvent \Laminas\Mvc\MvcEvent */
        if ($widget instanceof WidgetInterface) {
            $mvcEvent = $serviceLocator->get('application')->getMvcEvent();
            $widget->setServiceLocator($serviceLocator);
            $widget->setView($serviceLocator->get('ViewRenderer'));
            $widget->setMvcEvent($mvcEvent);

            if ($widget->isInitialised()) {
                return true;
            }

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
