<?php
namespace PageBuilderTest\Controller;

use PageBuilder\Controller\PageController;
use PageBuilder\View\Helper\PageBuilder;
use PageBuilderTest\Bootstrap;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Console\Router\RouteMatch;
use Zend\View\Model\ViewModel;

/**
 * Class GridControllerTest
 * @package SynergyDataGridTest\Controller
 */
class PageControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testControllerInstance()
    {
        /** @var PageController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(PageController::class);
        $this->assertInstanceOf(PageController::class, $controller);
    }

    public function testCanAccessBuilderPlugin()
    {
        /** @var PageController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(PageController::class);
        $plugin     = $controller->buildPage();
        $this->assertInstanceOf(PageBuilder::class, $plugin);
    }

    /**
     * @dataProvider getRestMethods
     */
    public function testRestMethods($method, $id, $data = [])
    {

        /** @var PageController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(PageController::class);

        $params = [
            'entity' => 'page',
        ];

        if ($id) {
            $params['id'] = $id;
        }

        $request = new Request();
        $router  = new RouteMatch($params);
        $request->setMethod($method);

        if (empty($id) or is_array($id)) {
            $request->setRequestUri('/pagebuilder/layout/page');
        } else {
            $request->setRequestUri('/pagebuilder/layout/page/' . $id);
        }

        if ($data) {
            $request->setContent(http_build_query($data));
        }

        $controller->getEvent()->setRouteMatch($router);
        $model = $controller->dispatch($request);

        $this->assertInstanceOf(ViewModel::class, $model);
    }

    public function getRestMethods()
    {
        return [
            ['GET', '1'],
        ];
    }
}
