<?php
namespace PageBuilder\Event\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class PageBuilderListener
	implements ListenerAggregateInterface {
	protected $listeners = array();

	/** @var \Zend\ServiceManager\ServiceManager */
	protected $_serviceManager;

	public function __construct( $serviceManager ) {
		$this->_serviceManager = $serviceManager;
	}

	public function attach( EventManagerInterface $events ) {

		$this->listeners[] = $events->attach( MvcEvent::EVENT_DISPATCH, array( $this, 'initialiseWidgets' ), - 2 );
		$this->listeners[] = $events->attach(
			array(
				MvcEvent::EVENT_RENDER_ERROR,
				MvcEvent::EVENT_DISPATCH_ERROR
			),
			array( $this, 'renderErrorPage' ),
			9999
		);
	}

	public function detach( EventManagerInterface $events ) {
		foreach ( $this->listeners as $index => $listener ) {
			if ( $events->detach( $listener ) ) {
				unset( $this->listeners[ $index ] );
			}
		}
	}

	public function renderErrorPage() {
		$errorPage = $this->findErrorPage();

		if ( $errorPage ) {
			/** @var $viewHelperManager \Zend\View\HelperPluginManager */
			$viewHelperManager = $this->_serviceManager->get( 'viewHelperManager' );

			/** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */
			$pageBuilder = $viewHelperManager->get( 'buildPage' );
			$activeTheme = $this->_serviceManager->get( 'active_theme' ) ?: null;
			$pageBuilder->init( $errorPage, null, $activeTheme );
		}
	}

	public function initialiseWidgets( MvcEvent $event ) {
		$moduleEnabled = false;

		if ( $app = $event->getApplication() ) {
			/** @var $viewHelperManager \Zend\View\HelperPluginManager */
			$viewHelperManager = $this->_serviceManager->get( 'viewHelperManager' );

			/** @var $helper \PageBuilder\View\Helper\PageBuilder */
			$helper = $viewHelperManager->get( 'buildPage' );

			/** @var $options \PageBuilder\View\Helper\Config\PageBuilderConfig */
			$options    = $helper->getOptions();
			$controller = $event->getRouteMatch()->getParam( 'controller' );
			list( $module, ) = explode( '\\', $controller );

			$enabledModules = $options->getModules();

			if ( empty( $enabledModules ) || in_array( $module, $enabledModules ) ) {
				$moduleEnabled = true;
			}

			if ( $options->getEnabled() && $moduleEnabled && $options->getMainNavigation() ) {

				/** @var $navigation \Zend\View\Helper\Navigation */
				/** @var $menuTree \Zend\View\Helper\Navigation */
				$navigation = $viewHelperManager->get( 'navigation' );
				$menuTree   = $navigation( $options->getMainNavigation() );
				$container  = $menuTree->getContainer();
				$activeMenu = $navigation->findActive( $container );

				if ( $activeMenu ) {
					/** @var $model \pageBuilder\Model\PageModel */
					/** @var $activeTheme \SynergyCommon\Entity\AbstractEntity */
					/** @var $pageBuilder \PageBuilder\View\Helper\PageBuilder */

					$activeTheme = $this->_serviceManager->get( 'active_theme' ) ?: null;
					$model       = $this->_serviceManager->get( 'pagebuilder\model\page' );
					$menu        = $model->getMainPageById( $activeMenu['page']->id );
					$pageBuilder = $viewHelperManager->get( 'buildPage' );

					$pageBuilder->init( $menu, $menuTree, $activeTheme );
				}
			}
		}
	}

	/**
	 * @return \SynergyCommon\Entity\BasePage
	 */
	private function findErrorPage() {
		/** @var $viewHelperManager \Zend\View\HelperPluginManager */
		$viewHelperManager = $this->_serviceManager->get( 'viewHelperManager' );

		/** @var $navigation \Zend\View\Helper\Navigation */
		$navigation = $viewHelperManager->get( 'navigation' );

		/** @var $helper \PageBuilder\View\Helper\PageBuilder */
		$helper = $viewHelperManager->get( 'buildPage' );

		/** @var $options \PageBuilder\View\Helper\Config\PageBuilderConfig */
		$options = $helper->getOptions();

		/** @var $menuTree \Zend\View\Helper\Navigation */
		$menuTree = $navigation( $options->getMainNavigation() );

		$errorPage = $menuTree->getContainer()->findOneBy( 'privilege', 'error-page' );
		/** @var $model \pageBuilder\Model\PageModel */
		$model = $this->_serviceManager->get( 'pagebuilder\model\page' );

		if ( empty( $errorPage ) ) {
			/** @var $errorPage \SynergyCommon\Entity\BasePage */
			$errorPage = $model->findOneBy( array( 'slug' => 'error-page' ) );
		} else {
			$errorPage = $model->findObject( $errorPage->getId() );
		}

		return $errorPage;
	}
}
