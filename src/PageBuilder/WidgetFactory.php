<?php
namespace PageBuilder;

use Interop\Container\ContainerInterface;
use Zend\Http\Response;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class WidgetFactory
 *
 * @package PageBuilder
 */
class WidgetFactory implements AbstractFactoryInterface
{
    const WIDGET_SUFFIX = 'widget';
    protected $_config = array();
    public static $registry = array();

    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $serviceLocator, $requestedName)
    {
        if (substr($requestedName, -6) == self::WIDGET_SUFFIX) {
            /** @var $util \PageBuilder\Util\Widget */
            $util     = $serviceLocator->get('util\widget');
            $widgetId = str_replace(self::WIDGET_SUFFIX, '', $requestedName);

            return $util->widgetExist($widgetId);
        }

        return false;
    }

    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return $this|bool|BaseWidget
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $serviceLocator \Zend\Servicemanager\ServiceManager */
        $widgetId = str_replace(self::WIDGET_SUFFIX, '', $requestedName);

        /** @var $util \PageBuilder\Util\Widget */
        $util = $serviceLocator->get('util\widget');

        if ($data = $util->widgetExist($widgetId, $serviceLocator)) {
            /** @var $widget \PageBuilder\BaseWidget */
            $widget = new $data['class']();
            $widget->setId($widgetId);

            /** @var $mvcEvent \Zend\Mvc\MvcEvent */
            $mvcEvent = $serviceLocator->get('application')->getMvcEvent();

            /** @var $viewhelper \Zend\View\Renderer\RendererInterface */
            $viewhelper = $serviceLocator->get('viewrenderer');

            $widget->setServiceManager($serviceLocator);
            $widget->setView($viewhelper);
            $widget->setMvcEvent($mvcEvent);
            $response = $widget->init();

            //send response if we have a widget returning a response instance
            if ($response instanceof Response) {
                $mvcEvent->stopPropagation();

                return $response;
            } else {
                return $widget;
            }
        }
        return false;
    }
}
