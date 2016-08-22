<?php
namespace PageBuilderTest\Controller;

use PageBuilder\Controller\TemplateController;
use PageBuilderTest\Bootstrap;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Console\Router\RouteMatch;
use Zend\View\Model\ViewModel;

/**
 * Class TemplateControllerTest
 * @package PageBuilderTest\Controller
 */
class TemplateControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testControllerInstance()
    {
        /** @var TemplateController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(TemplateController::class);
        $this->assertInstanceOf(TemplateController::class, $controller);
    }

    /**
     * @dataProvider getRestMethods
     */
    public function testRestMethods($method, $id, $data = [])
    {

        /** @var TemplateController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(TemplateController::class);

        $params = [
            'entity' => 'template',
        ];

        if ($id) {
            $params['id'] = $id;
        }

        $request = new Request();
        $router  = new RouteMatch($params);
        $request->setMethod($method);

        if (empty($id) or is_array($id)) {
            $request->setRequestUri('/pagebuilder/layout/template');
        } else {
            $request->setRequestUri('/pagebuilder/layout/template/' . $id);
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
            ['PUT', '1', []],
            ['PUT', '1', ['title' => 'test item', 'description' => 'phpunit']],
        ];
    }
}
