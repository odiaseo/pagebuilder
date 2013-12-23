<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageBuilder;


use Zend\Mvc\MvcEvent;

class Module
{

    public function init($moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'onModuleDispatch'), 103);
        $sharedEvents->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'initEntityManager'), 103);
    }


    public function onModuleDispatch($e)
    {
        if ($app = $e->getApplication()) {
            $locator = $app->getServiceManager();

            $viewHelperManager = $locator->get('viewHelperManager');
            $navigation        = $viewHelperManager->get('navigation');
            $menuTree          = $navigation('pagebuilder\menu');
            $container         = $menuTree->getContainer();
            $activeMenu        = $navigation->findActive($container);

            if ($activeMenu) {
                $activeTheme = $locator->get('active_theme');
                $menu        = $locator->get('pages_service')->findObject($activeMenu['page']->id);

                $pageBuilder = $viewHelperManager->get('buildPage');
                $pageBuilder->preparePageItems($menu, $menuTree, $activeTheme);
            }
        }
    }

    public function initEntityManager($e)
    {
        $sm = $e->getApplication()->getServiceManager();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $sm->get('doctrine.entitymanager.orm_default');

        $em->getFilters()->enable("site-specific")->setServiceManager($sm);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
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
                'pages_service'     => __NAMESPACE__ . '\Service\PageService',
                'component_service' => __NAMESPACE__ . '\Service\ComponentService',
                'template_service'  => __NAMESPACE__ . '\Service\TemplateService',
                'widget_service'    => __NAMESPACE__ . '\Service\WidgetService',
                'menu'              => __NAMESPACE__ . '\Navigation\NavigationFactory',
            ),
            'initializers' => array(
                'widget' => function ($widget, $sm) {
                    /** @var $widget \PageBuilder\BaseWidget */
                    if ($widget instanceof WidgetInterface) {
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
                __NAMESPACE__ . '\Service\PageService'          => __NAMESPACE__ . '\Service\PageService',
                __NAMESPACE__ . '\Navigation\NavigationFactory' => __NAMESPACE__ . '\Navigation\NavigationFactory',
                __NAMESPACE__ . '\Service\PageService'          => __NAMESPACE__ . '\Service\PageService',
                __NAMESPACE__ . '\Service\ComponentService'     => __NAMESPACE__ . '\Service\ComponentService',
                __NAMESPACE__ . '\Service\WidgetService'        => __NAMESPACE__ . '\Service\WidgetService',
                __NAMESPACE__ . '\Service\TemplateService'      => __NAMESPACE__ . '\Service\TemplateService',
                __NAMESPACE__ . '\WidgetDataFactory'            => __NAMESPACE__ . '\WidgetDataFactory',
            )
        );
    }


    public function getViewHelperConfig()
    {
        return array(

            'factories'  => array(

                'flashMessages' => function ($sm) {
                    $flashmessenger = $sm->getServiceLocator()
                        ->get('ControllerPluginManager')
                        ->get('flashmessenger');

                    $messages = new FlashMessages();
                    $messages->setFlashMessenger($flashmessenger);

                    return $messages;
                },


                'microData'     => function ($sm) {
                    $microData = new MicroData();
                    $microData->setServiceManager($sm);

                    return $microData;

                },

            ),
            'invokables' => array(
                'buildPage' => __NAMESPACE__ . '\View\Helper\PageBuilder',
            )
        );
    }
}