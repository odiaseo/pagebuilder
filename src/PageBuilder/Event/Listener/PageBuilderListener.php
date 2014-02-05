<?php
namespace PageBuilder\Event\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class PageBuilderListener
    implements ListenerAggregateInterface
{
    protected $listeners = array();

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initialiseWidgets'), -2);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function initialiseWidgets(MvcEvent $event)
    {
        if ($app = $event->getApplication()) {
            $locator = $app->getServiceManager();
            $config  = $locator->get('config');

            $enabled = (!empty($config['pagebuilder']['enabled'])  and $config['pagebuilder']['enabled']) ? true
                : false;

            if ($enabled and $mainMenuKey = $config['pagebuilder']['main_navigation']) {

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
}