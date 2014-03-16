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

    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function __construct($serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initialiseWidgets'), -2);
        $this->listeners[] = $events->attach(
            array(
                 MvcEvent::EVENT_RENDER_ERROR,
                 MvcEvent::EVENT_DISPATCH_ERROR
            ),
            array($this, 'renderErrorPage'),
            9999
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function renderErrorPage()
    {
        /** @var $model \pageBuilder\Model\PageModel */
        $model = $this->_serviceManager->get('pagebuilder\model\page');

        /** @var $errorPage \SynergyCommon\Entity\BasePage */
        $errorPage = $model->getRepository()->findOneBy(array('slug' => 'error-page'));

        if ($errorPage) {
            /** @var $viewHelperManager \Zend\View\HelperPluginManager */
            $viewHelperManager = $this->_serviceManager->get('viewHelperManager');

            /** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */
            $pageBuilder = $viewHelperManager->get('buildPage');
            $pageBuilder->init($errorPage, null, null);
        }
    }

    public function initialiseWidgets(MvcEvent $event)
    {
        if ($app = $event->getApplication()) {
            /** @var $viewHelperManager \Zend\View\HelperPluginManager */
            $viewHelperManager = $this->_serviceManager->get('viewHelperManager');

            /** @var $helper \PageBuilder\View\Helper\PageBuilder */
            $helper = $viewHelperManager->get('buildPage');

            /** @var $options \PageBuilder\View\Helper\Config\PageBuilderConfig */
            $options = $helper->getOptions();

            if ($options->getEnabled() and $options->getMainNavigation()) {

                /** @var $navigation \Zend\View\Helper\Navigation */
                $navigation = $viewHelperManager->get('navigation');

                /** @var $menuTree \Zend\View\Helper\Navigation */
                $menuTree  = $navigation($options->getMainNavigation());
                $container = $menuTree->getContainer();

                $activeMenu = $navigation->findActive($container);

                if ($activeMenu) {
                    /** @var $activeTheme \SynergyCommon\Entity\AbstractEntity */
                    $activeTheme = $this->_serviceManager->get('active_theme') ? : null;

                    /** @var $model \pageBuilder\Model\PageModel */
                    $model = $this->_serviceManager->get('pagebuilder\model\page');

                    /** @var $menu \SynergyCommon\Entity\BasePage */
                    $menu = $model->findObject($activeMenu['page']->id);

                    /** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */
                    $pageBuilder = $viewHelperManager->get('buildPage');
                    $pageBuilder->init($menu, $menuTree, $activeTheme);
                }
            }
        }
    }
}