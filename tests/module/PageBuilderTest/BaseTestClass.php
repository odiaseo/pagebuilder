<?php
namespace PageBuilderTest;

use PageBuilder\Controller\PageController;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;

class BaseTestClass extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    /** @var \Zend\Mvc\Application */
    protected $_app;

    /** @var \Doctrine\Orm\EntityManager */
    protected $_em;


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
        $this->_serviceManager = \Bootstrap::getServiceManager();


        $this->controller = new PageController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array());
        $this->event      = new MvcEvent();
        $config           = $this->_serviceManager->get('Config');
        $routerConfig     = isset($config['router']) ? $config['router'] : array();
        $router           = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->_serviceManager);
    }
}