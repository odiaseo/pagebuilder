<?php
namespace PageBuilderTest\Servicee;

use PageBuilder\Controller\PageController;
use PageBuilderTest\Bootstrap;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\Http\TreeRouteStack as HttpRouter;
use Laminas\Router\RouteMatch;

/**
 * @backupGlobals disabled
 */
class PageTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Laminas\ServiceManager\ServiceManager */
    protected $serviceManager;

    /** @var \Laminas\Mvc\Application */
    protected $app;

    /** @var \Doctrine\Orm\EntityManager */
    protected $entityManager;

    protected $controller;

    /** @var \Laminas\Http\PhpEnvironment\Request $request */
    protected $request;

    protected $response;

    /** @var  \Laminas\Router\RouteMatch */
    protected $routeMatch;

    /** @var  \Laminas\Mvc\MvcEvent */
    protected $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceManager = Bootstrap::getServiceManager();

        $this->controller = new PageController($this->serviceManager);
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
        $pageService = $this->serviceManager->get('active\site');
        $this->assertInstanceOf('PageBuilder\Entity\Site', $pageService);
    }

    public function testService()
    {
        $pageService = $this->serviceManager->get('pagebuilder\service\layout');
        $this->assertInstanceOf('PageBuilder\Service\LayoutService', $pageService);
    }

    public function testPageBuilder()
    {
        $pageService = $this->serviceManager->get('ViewHelperManager')->get('buildPage');
        $this->assertInstanceOf('PageBuilder\View\Helper\PageBuilder', $pageService);
    }

    public function testGridInstance()
    {
        $grid = $this->serviceManager->get('jqgrid');
        $this->assertInstanceOf('SynergyDataGrid\Grid\GridType\DoctrineORMGrid', $grid);
    }
}
