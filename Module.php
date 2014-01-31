<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageBuilder;


use PageBuilder\Event\Listener\PageBuilderListener;
use Zend\Http\Response;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
    implements DependencyIndicatorInterface
{

    public function getModuleDependencies()
    {
        return array('SynergyDataGrid');
    }


    public function onBootstrap(MvcEvent $e)
    {
        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->attach(new PageBuilderListener());
    }


    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {

        return array(
            'aliases'      => array(
                'pages_service'     => 'PageBuilder\Service\PageService',
                'component_service' => 'PageBuilder\Service\ComponentService',
                'template_service'  => 'PageBuilder\Service\TemplateService',
                'widget_service'    => 'PageBuilder\Service\WidgetService',
                'pagebuilder\menu'  => 'PageBuilder\Navigation\NavigationFactory',
                'util\widget'       => 'PageBuilder\Util\Widget',
            ),
            'initializers' => array(
                'widget' => function ($widget, $sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceManager */

                    /** @var $widget  \object */
                    if ($widget instanceof WidgetInterface) {
                        /** @var $mvcEvent \Zend\Mvc\MvcEvent */
                        $mvcEvent = $sm->get('application')->getMvcEvent();
                        $widget->setServiceManager($sm);
                        $widget->setView($sm->get('viewrenderer'));
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
            ),

            'factories'    => array(
                'active_site'                              => 'SynergyCommon\Service\ActiveClientSite',
                'PageBuilder\Service\PageService'          => 'PageBuilder\Service\PageService',
                'PageBuilder\Navigation\NavigationFactory' => 'PageBuilder\Navigation\NavigationFactory',
                'PageBuilder\Service\ComponentService'     => 'PageBuilder\Service\ComponentService',
                'PageBuilder\Service\WidgetService'        => 'PageBuilder\Service\WidgetService',
                'PageBuilder\Service\TemplateService'      => 'PageBuilder\Service\TemplateService',
                'PageBuilder\WidgetDataFactory'            => 'PageBuilder\WidgetDataFactory',
                'active_theme'                             => function ($sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceManager */
                    $site  = $sm->get('active_site');
                    $theme = $sm->get('pagebuilder\model\theme')->getActiveTheme($site->getId());

                    return $theme;
                },
            )
        );
    }


    public function getViewHelperConfig()
    {
        return array(
            'factories'  => array(
                'flashMessages' => 'SynergyCommon\View\Helper\FlashMessages',
                'microData'     => 'SynergyCommon\View\Helper\MicroData',
            ),
            'invokables' => array(
                'buildPage' => 'PageBuilder\View\Helper\PageBuilder',
            )
        );
    }
}