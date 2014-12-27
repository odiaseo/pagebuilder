<?php
namespace PageBuilderTest\Servicee;

use PageBuilderTest\BaseTestClass;
use Zend\Mvc\Router\Console\RouteMatch;

/**
 * @backupGlobals disabled
 */
class PageTest extends BaseTestClass {
	/** @var \SynergyDataGrid\Grid\GridType\DoctrineORMGrid; */
	protected $_grid;

	public function testService() {
		$pageService = $this->_serviceManager->get( 'pagebuilder\service\layout' );
		$this->assertInstanceOf( 'PageBuilder\Service\LayoutService', $pageService );
	}

	public function testPageBuilder() {
		/** @var \Zend\Mvc\MvcEvent $mvcEvent */
		$mvcEvent   = $this->_serviceManager->get( 'application' )->getMvcEvent();
		$routeMatch = new RouteMatch( array( 'host' => 'builder-dev.com' ) );
		$mvcEvent->setRouteMatch( $routeMatch );

		$pageService = $this->_serviceManager->get( 'viewhelpermanager' )->get( 'buildpage' );
		$this->assertInstanceOf( 'PageBuilder\View\Helper\PageBuilder', $pageService );
	}

}
