<?php

namespace PageBuilder\Event\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\MvcEvent;

/**
 * Class PageBuilderListener.
 */
class PageBuilderListener implements ListenerAggregateInterface
{
    protected $listeners = [];

    /** @var \Laminas\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function __construct($serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'initialiseWidgets'], -2);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'renderErrorPage'], 9998);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'renderErrorPage'], 9999);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    public function renderErrorPage()
    {
        $errorPage = $this->findErrorPage();
        $request   = $this->_serviceManager->get('request');

        if ($errorPage and $request instanceof Request) {
            /** @var $viewHelperManager \Laminas\View\HelperPluginManager */
            $viewHelperManager = $this->_serviceManager->get('ViewHelperManager');

            /** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */
            $pageBuilder = $viewHelperManager->get('buildPage');
            $activeTheme = $this->_serviceManager->get('active\theme') ?: null;
            $pageBuilder->init($errorPage->getId(), null, $activeTheme);
        }
    }

    public function initialiseWidgets(MvcEvent $event)
    {
        /* @var Request $request */
        $moduleEnabled = false;
        $request       = $this->_serviceManager->get('request');

        if ($request instanceof Request and !$request->isXmlHttpRequest() and $app = $event->getApplication()) {
            /** @var $viewHelperManager \Laminas\View\HelperPluginManager */
            $viewHelperManager = $this->_serviceManager->get('ViewHelperManager');

            /** @var $helper \PageBuilder\View\Helper\PageBuilder */
            $helper = $viewHelperManager->get('buildPage');

            /** @var $options \PageBuilder\View\Helper\Config\PageBuilderConfig */
            $options    = $helper->getOptions();
            $controller = $event->getRouteMatch()->getParam('controller');
            list($module) = explode('\\', $controller);

            $enabledModules = $options->getModules();

            if (empty($enabledModules) || in_array($module, $enabledModules)) {
                $moduleEnabled = true;
            }

            if ($options->getEnabled() and $moduleEnabled and $options->getMainNavigation()) {

                /** @var $navigation \Laminas\View\Helper\Navigation */
                /* @var $menuTree \Laminas\View\Helper\Navigation */
                $navigation = $viewHelperManager->get('navigation');
                $menuTree   = $navigation($options->getMainNavigation());
                $container  = $menuTree->getContainer();
                $activeMenu = $navigation->findActive($container);

                if ($activeMenu) {
                    $pageId = $activeMenu['page']->id;
                } else {
                    $pageId = $this->_serviceManager->get('active\site')->getRootPage()->getId();
                }
                /* @var $model \pageBuilder\Model\PageModel */
                /** @var $activeTheme \SynergyCommon\Entity\AbstractEntity */
                /* @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */

                $activeTheme = $this->_serviceManager->get('active\theme') ?: null;
                $pageBuilder = $viewHelperManager->get('buildPage');

                $pageBuilder->init($pageId, $menuTree, $activeTheme);
            }
        }
    }

    /**
     * @return \SynergyCommon\Entity\BasePage
     */
    private function findErrorPage()
    {
        /** @var $viewHelperManager \Laminas\View\HelperPluginManager */
        $viewHelperManager = $this->_serviceManager->get('ViewHelperManager');

        /** @var $navigation \Laminas\View\Helper\Navigation */
        $navigation = $viewHelperManager->get('navigation');

        /** @var $helper \PageBuilder\View\Helper\PageBuilder */
        $helper = $viewHelperManager->get('buildPage');

        /** @var $options \PageBuilder\View\Helper\Config\PageBuilderConfig */
        $options = $helper->getOptions();

        /** @var $menuTree \Laminas\View\Helper\Navigation */
        $menuTree = $navigation($options->getMainNavigation());

        $errorPage = $menuTree->getContainer()->findOneBy('privilege', 'error-page');
        /** @var $model \pageBuilder\Model\PageModel */
        $model = $this->_serviceManager->get('pagebuilder\model\page');

        if (empty($errorPage)) {
            /** @var $errorPage \SynergyCommon\Entity\BasePage */
            $errorPage = $model->findOneBy(['slug' => 'error-page']);
        } else {
            $errorPage = $model->findObject($errorPage->getId());
        }

        return $errorPage;
    }
}
