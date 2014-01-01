<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageBuilder;


use PageBuilder\View\Helper\FlashMessages;
use PageBuilder\View\Helper\MicroData;
use Zend\Console\Request;
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

    public function init($moduleManager)
    {
        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'onModuleDispatch'), 103);
        $sharedEvents->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, array($this, 'initEntityManager'), 103);
    }

    public function onBootstrap(MvcEvent $e)
    {
        /** @var $eventManager \Zend\EventManager\EventManager */
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }


    public function onModuleDispatch(MvcEvent $e)
    {
        if ($app = $e->getApplication()) {
            $locator = $app->getServiceManager();
            $config  = $locator->get('config');

            if ($mainMenuKey = $config['pagebuilder']['main_navigation']) {

                $viewHelperManager = $locator->get('viewHelperManager');

                /** @var $navigation \Zend\View\Helper\Navigation */
                $navigation = $viewHelperManager->get('navigation');

                /** @var $menuTree \Zend\View\Helper\Navigation */
                $menuTree  = $navigation($mainMenuKey);
                $container = $menuTree->getContainer();

                $activeMenu = $navigation->findActive($container);

                if ($activeMenu) {
                    /** @var $activeTheme \SynergyCommon\Entity\AbstractEntity */
                    $activeTheme = $locator->get('active_theme');
                    $menu        = $locator->get('pagebuilder\model\page')->findObject($activeMenu['page']->id);

                    /** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */
                    $pageBuilder = $viewHelperManager->get('buildPage');
                    $pageBuilder->init($menu, $menuTree, $activeTheme);
                }
            }
        }
    }

    public function initEntityManager(MvcEvent $e)
    {
        /** @var  $sm \Zend\Servicemanager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $sm->get('doctrine.entitymanager.orm_default');

        /** @var $siteFilter \SynergyCommon\Doctrine\Filter\SiteFilter */
        $siteFilter = $em->getFilters()->enable("site-specific");
        $siteFilter->setServiceManager($sm);
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
                'pages_service'     => __NAMESPACE__ . '\Service\PageService',
                'component_service' => __NAMESPACE__ . '\Service\ComponentService',
                'template_service'  => __NAMESPACE__ . '\Service\TemplateService',
                'widget_service'    => __NAMESPACE__ . '\Service\WidgetService',
                'pagebuilder\menu'  => __NAMESPACE__ . '\Navigation\NavigationFactory',
                'util\widget'       => __NAMESPACE__ . '\Util\Widget',
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
                __NAMESPACE__ . '\Service\PageService'          => __NAMESPACE__ . '\Service\PageService',
                __NAMESPACE__ . '\Navigation\NavigationFactory' => __NAMESPACE__ . '\Navigation\NavigationFactory',
                __NAMESPACE__ . '\Service\PageService'          => __NAMESPACE__ . '\Service\PageService',
                __NAMESPACE__ . '\Service\ComponentService'     => __NAMESPACE__ . '\Service\ComponentService',
                __NAMESPACE__ . '\Service\WidgetService'        => __NAMESPACE__ . '\Service\WidgetService',
                __NAMESPACE__ . '\Service\TemplateService'      => __NAMESPACE__ . '\Service\TemplateService',
                __NAMESPACE__ . '\WidgetDataFactory'            => __NAMESPACE__ . '\WidgetDataFactory',

                'active_theme'                                  => function ($sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceManager */
                    $site  = $sm->get('active_site');
                    $theme = $sm->get('pagebuilder\model\theme')->getActiveTheme($site->getId());

                    return $theme;
                },

                'active_site'                                   => function ($sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceManager */
                    $containerKey = 'active-site';
                    $request      = $sm->get('application')->getRequest();

                    if ($request instanceof Request) {
                        /** @var $event \Zend\Mvc\MvcEvent */
                        $event = $sm->get('application')->getMvcEvent();
                        /** @var $rm \Zend\Mvc\Router\RouteMatch */
                        $rm   = $event->getRouteMatch();
                        $host = $rm->getParam('host');
                    } else {
                        /** @var $request \Zend\Http\PhpEnvironment\Request */
                        $host = $request->getServer('HTTP_HOST');
                    }

                    $hostname   = str_replace(array('http://', 'https://', 'www.'), '', $host);
                    $sessionKey = preg_replace('/[^\p{L}\p{N}]+/ui', '', "host{$hostname}");

                    /** @var $container \ArrayObject */
                    $container = new Container($sessionKey);

                    if ($container->offsetExists($containerKey)) {
                        $em   = $sm->get('doctrine.entitymanager.orm_default');
                        $site = $container->offsetGet($containerKey);
                        $site = $em->merge($site);
                    } elseif (!$site = $sm->get('pagebuilder\model\site')->findOneByDomain($hostname)) {
                        header
                        (
                            'HTTP/1.1 403 Application Error'
                        );
                        echo "Site is not registered";
                        exit;
                    } else {
                        $container->offsetSet($containerKey, $site);
                    }

                    return $site;
                },
            )
        );
    }


    public function getViewHelperConfig()
    {
        return array(
            'factories'  => array(
                'flashMessages' => function ($sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceLocatorAwareInterface */
                    /** @var  $serviceManager \Zend\Servicemanager\ServiceManager */
                    $serviceManager = $sm->getServiceLocator();
                    $flashmessenger = $serviceManager->get('ControllerPluginManager')->get('flashmessenger');

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