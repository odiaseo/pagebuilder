<?php
namespace PageBuilderTest\Servicee;

use PageBuilder\Controller\PageController;
use PageBuilderTest\Bootstrap;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;

/**
 * @backupGlobals disabled
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    /** @var \Zend\Mvc\Application */
    protected $app;

    /** @var \Doctrine\Orm\EntityManager */
    protected $entityManager;

    protected $controller;

    /** @var \Zend\Http\PhpEnvironment\Request $request */
    protected $request;

    protected $response;

    /** @var  \Zend\Mvc\Router\RouteMatch */
    protected $routeMatch;

    /** @var  \Zend\Mvc\MvcEvent */
    protected $event;

    public function setUp()
    {
        parent::setUp();
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->controller = new PageController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array());
        $this->event      = new MvcEvent();
        $config           = Bootstrap::getServiceManager()->get('Config');
        $routerConfig     = isset($config['router']) ? $config['router'] : array();
        $router           = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->serviceManager);
    }

    public function testLocalSite()
    {
        $pageService = $this->serviceManager->get('active\Site');
        $this->assertInstanceOf('PageBuilder\Entity\Site', $pageService);
    }

    public function testService()
    {
        $pageService = $this->serviceManager->get('pagebuilder\service\layout');
        $this->assertInstanceOf('PageBuilder\Service\LayoutService', $pageService);
    }

    public function testPageBuilder()
    {
        $pageService = $this->serviceManager->get('viewhelpermanager')->get('buildpage');
        $this->assertInstanceOf('PageBuilder\View\Helper\PageBuilder', $pageService);
    }

    public function testGridInstance()
    {
        $grid = $this->serviceManager->get('jqgrid');
        $this->assertInstanceOf('SynergyDataGrid\Grid\GridType\DoctrineORMGrid', $grid);
    }
}
