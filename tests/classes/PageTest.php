<?php
namespace PageBuilderTest\Classes;


use PageBuilderTest\BaseTestClass;
use PageBuilderTest\Bootstrap;

/**
 * @backupGlobals disabled
 */
class PageTest extends BaseTestClass
{
    /** @var \SynergyDataGrid\Grid\GridType\DoctrineORMGrid; */
    protected $_grid;

    protected $_serviceManager;

    public function setUp()
    {
        parent::setUp();
        $this->_serviceManager = Bootstrap::getServiceManager();

    }

    public function testService()
    {
        $pageService = $this->_serviceManager->get('pagebuilder\service\page');
        $this->assertInstanceOf('PageBuilder\Service\BaseService', $pageService);
    }

}