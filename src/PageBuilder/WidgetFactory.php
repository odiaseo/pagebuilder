<?php
namespace PageBuilder;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class WidgetFactory
    implements AbstractFactoryInterface, ServiceManagerAwareInterface
{
    const WIDGET_SUFFIX = 'widget';
    protected $_config = array();
    public static $registry = array();

    /** @var \Zend\Servicemanager\ServiceManager */
    protected $_sm;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_sm = $serviceManager;
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
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
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $widgetId = str_replace(self::WIDGET_SUFFIX, '', $name);

        /** @var $util \PageBuilder\Util\Widget */
        $util = $serviceLocator->get('util\widget');

        if ($data = $util->widgetExist($widgetId, $serviceLocator)) {
            /** @var $widget \PageBuilder\BaseWidget */
            $widget = new $data['class']();
            $widget->setId($widgetId);

            return $widget;
        }

        return false;

    }
}