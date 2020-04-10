<?php
namespace PageBuilderTest\Controller;

use PageBuilder\Controller\TemplateSectionController;
use PageBuilderTest\Bootstrap;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\Console\Router\RouteMatch;
use Laminas\View\Model\ViewModel;

/**
 * Class TemplateSectionControllerTest
 * @package PageBuilderTest\Controller
 */
class TemplateSectionControllerTest extends \PHPUnit\Framework\TestCase
{
    protected $serviceManager;

    public function setUp()
    {

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testControllerInstance()
    {
        /** @var TemplateSectionController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(TemplateSectionController::class);
        $this->assertInstanceOf(TemplateSectionController::class, $controller);
    }

    /**
     * @dataProvider getRestMethods
     */
    public function testRestMethods($method, $id, $data = [])
    {

        /** @var TemplateSectionController $controller */
        $controller = $this->serviceManager->get('ControllerManager')->get(TemplateSectionController::class);

        $params = [
            'entity' => 'template',
        ];

        if ($id) {
            $params['id'] = $id;
        }

        $request = new Request();
        $router  = new RouteMatch($params);
        $request->setMethod($method);

        $request->setRequestUri("/pagebuilder/template/{$id}/section");

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
            ['PUT', '1', ['sections' => []]],
        ];
    }
}
