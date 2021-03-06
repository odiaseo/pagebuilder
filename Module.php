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
use PageBuilder\Service\ActiveThemeFactory;
use PageBuilder\Service\Factory\RedisCacheAdapterFactory;
use PageBuilder\Service\WidgetInitializer;
use PageBuilder\Service\WidgetUtilFactory;
use PageBuilder\Session\SessionStorageFactory;
use PageBuilder\View\Helper\PageBuilderInitializer;
use SynergyCommon\Event\Listener\SynergyModuleListener;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 *
 */
class Module implements DependencyIndicatorInterface
{
    public function getModuleDependencies()
    {
        return ['SynergyDataGrid'];
    }

    public function onBootstrap(MvcEvent $e)
    {
        /** @var $eventManager \Laminas\EventManager\EventManager */
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceLocator = $e->getApplication()->getServiceManager();
        $listener       = new PageBuilderListener($serviceLocator);
        $listener->attach($eventManager);

        /** @var $serviceLocator \Laminas\ServiceManager\ServiceManager */
        $serviceLocator = $e->getApplication()->getServiceManager();

        $synergyListener = new SynergyModuleListener();
        $synergyListener->attach($eventManager);

        $synergyListener->initSession($e);
        $synergyListener->bootstrap($eventManager, $serviceLocator);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {

        return [
            'aliases'      => [
                'pages_service'     => 'PageBuilder\Service\PageService',
                'component_service' => 'PageBuilder\Service\ComponentService',
                'template_service'  => 'PageBuilder\Service\TemplateService',
                'widget_service'    => 'PageBuilder\Service\WidgetService',
                'pagebuilder\menu'  => 'PageBuilder\Navigation\NavigationFactory',
                'util\widget'       => 'PageBuilder\Util\Widget',
                'session_manager'   => 'Laminas\Session\SessionManager',
                'active\site'       => 'PageBuilder\Service\LocalSiteFactory',
            ],
            'initializers' => [
                'widget' => WidgetInitializer::class,
            ],

            'factories' => [
                'PageBuilder\Service\LocalSiteFactory'      => 'PageBuilder\Service\LocalSiteFactory',
                'PageBuilder\Service\PageService'           => 'PageBuilder\Service\PageService',
                'PageBuilder\Navigation\NavigationFactory'  => 'PageBuilder\Navigation\NavigationFactory',
                'PageBuilder\Service\ComponentService'      => 'PageBuilder\Service\ComponentService',
                'PageBuilder\Service\WidgetService'         => 'PageBuilder\Service\WidgetService',
                'PageBuilder\Service\TemplateService'       => 'PageBuilder\Service\TemplateService',
                'PageBuilder\WidgetDataFactory'             => 'PageBuilder\WidgetDataFactory',
                'AffiliateManager\Service\LocalSiteFactory' => 'AffiliateManager\Service\LocalSiteFactory',
                'active\theme'                              => ActiveThemeFactory::class,
                'PageBuilder\Util\Widget'                   => WidgetUtilFactory::class,
                'synergy\session\storage'                   => SessionStorageFactory::class,
                'cache\adapter\redis'                       => RedisCacheAdapterFactory::class,
            ],
        ];
    }

    public function getControllerPluginConfig()
    {
        return [
            'invokables' => [
                'buildPage' => 'PageBuilder\Controller\Plugin\PageBuilder',
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'aliases'      => [
                'pageBuilder' => 'buildPage',
            ],
            'invokables'   => [
                'buildPage' => 'PageBuilder\View\Helper\PageBuilder',
            ],
            'initializers' => [
                'initbuilder' => PageBuilderInitializer::class,
            ],
        ];
    }
}
