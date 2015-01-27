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
use PageBuilder\Util\Widget;
use PageBuilder\View\Helper\Config\PageBuilderConfig;
use PageBuilder\View\Helper\PageBuilder;
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
        $synergyListener->initSession($e);
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
                'PageBuilder\Service\LocalSiteFactory'      => 'PageBuilder\Service\LocalSiteFactory',
                'PageBuilder\Service\PageService'           => 'PageBuilder\Service\PageService',
                'PageBuilder\Navigation\NavigationFactory'  => 'PageBuilder\Navigation\NavigationFactory',
                'PageBuilder\Service\ComponentService'      => 'PageBuilder\Service\ComponentService',
                'PageBuilder\Service\WidgetService'         => 'PageBuilder\Service\WidgetService',
                'PageBuilder\Service\TemplateService'       => 'PageBuilder\Service\TemplateService',
                'PageBuilder\WidgetDataFactory'             => 'PageBuilder\WidgetDataFactory',
                'Zend\Session\SessionManager'               => 'Zend\Session\Service\SessionManagerFactory',
                'AffiliateManager\Service\LocalSiteFactory' => 'AffiliateManager\Service\LocalSiteFactory',
                'active_theme'                              => function ($sm) {
                    /** @var  $sm \Zend\Servicemanager\ServiceManager */
                    $site  = $sm->get('active\site');
                    $theme = $sm->get('pagebuilder\model\theme')->getActiveTheme($site->getId());

                    return $theme;
                },

                'PageBuilder\Util\Widget'                   => function ($serviceManager) {
                    $widget = new Widget();
                    $widget->setServiceManager($serviceManager);
                    $widget->init();

                    return $widget;
                }

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
                'initbuilder' => function ($helper, $helperManager) {
                    /** @var $helperManager \Zend\View\HelperPluginManager */
                    /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
                    $serviceManager = $helperManager->getServicelocator();
                    if ($helper instanceof PageBuilder) {
                        $config = $serviceManager->get('config');

                        $formatters = array();
                        foreach ($config['pagebuilder']['output_formatters'] as $format) {
                            if (is_string($format)) {
                                $formatters[] = $serviceManager->get($format);
                            } elseif ($format instanceof FormatterInterface) {
                                $formatters[] = $format;
                            } elseif (is_callable($format)) {
                                $formatters[] = $format;
                            } else {
                                continue;
                            }
                        }

                        /** @var $theme \PageBuilder\Entity\Theme */

                        $theme         = $serviceManager->get('active_theme');
                        $builderConfig = $config['pagebuilder'];
                        if ($theme) {
                            $builderConfig['bootstrap_version'] = $theme->getBootstrapVersion();
                        }

                        if (is_string($builderConfig['replacements'])) {
                            $builderConfig['replacements'] = $serviceManager->get($builderConfig['replacements']);
                        }

                        $options = new PageBuilderConfig($builderConfig);
                        $options->setOutputFormatters($formatters);

                        $helper->setOptions($options);
                    }
                }
            )
        );
    }
}
