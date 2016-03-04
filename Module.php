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
use PageBuilder\Service\WidgetInitializer;
use PageBuilder\Service\WidgetUtilFactory;
use PageBuilder\View\Helper\PageBuilderInitializer;
use SynergyCommon\Event\Listener\SynergyModuleListener;
use Zend\Http\Response;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package PageBuilder
 */
class Module implements DependencyIndicatorInterface
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

        $serviceLocator = $e->getApplication()->getServiceManager();
        $eventManager->attach(new PageBuilderListener($serviceLocator));

        /** @var $serviceLocator \Zend\ServiceManager\ServiceManager */
        $serviceLocator = $e->getApplication()->getServiceManager();

        $synergyListener = new SynergyModuleListener();
        $eventManager->attach($synergyListener);
        $synergyListener->initmemcacSession($e);
        $synergyListener->bootstrap($eventManager, $serviceLocator);
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
                'session_manager'   => 'Zend\Session\SessionManager',
                'active\site'       => 'PageBuilder\Service\LocalSiteFactory',
            ),
            'initializers' => array(
                'widget' => WidgetInitializer::class,
            ),

            'factories'    => array(
                'PageBuilder\Service\LocalSiteFactory'      => 'PageBuilder\Service\LocalSiteFactory',
                'PageBuilder\Service\PageService'           => 'PageBuilder\Service\PageService',
                'PageBuilder\Navigation\NavigationFactory'  => 'PageBuilder\Navigation\NavigationFactory',
                'PageBuilder\Service\ComponentService'      => 'PageBuilder\Service\ComponentService',
                'PageBuilder\Service\WidgetService'         => 'PageBuilder\Service\WidgetService',
                'PageBuilder\Service\TemplateService'       => 'PageBuilder\Service\TemplateService',
                'PageBuilder\WidgetDataFactory'             => 'PageBuilder\WidgetDataFactory',
                'Zend\Session\SessionManager'               => 'Zend\Session\Service\SessionManagerFactory',
                'AffiliateManager\Service\LocalSiteFactory' => 'AffiliateManager\Service\LocalSiteFactory',
                'active_theme'                              => ActiveThemeFactory::class,
                'PageBuilder\Util\Widget'                   => WidgetUtilFactory::class,
            )
        );
    }

    public function getControllerPluginConfig()
    {
        return array(
            'invokables' => array(
                'buildPage' => 'PageBuilder\Controller\Plugin\PageBuilder'
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables'   => array(
                'buildPage'     => 'PageBuilder\View\Helper\PageBuilder',
                'flashMessages' => 'SynergyCommon\View\Helper\FlashMessages',
                'microData'     => 'SynergyCommon\View\Helper\MicroData',
            ),
            'initializers' => array(
                'initbuilder' => PageBuilderInitializer::class
            )
        );
    }
}
