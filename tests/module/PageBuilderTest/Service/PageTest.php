<?php
namespace PageBuilderTest\Servicee;

use PageBuilderTest\BaseTestClass;

/**
 * @backupGlobals disabled
 */
class PageTest extends BaseTestClass
{

    public function testLocalSite()
    {
        $pageService = $this->_serviceManager->get('active\Site');
        $this->assertInstanceOf('PageBuilder\Entity\Site', $pageService);
    }

    public function testService()
    {
        $pageService = $this->_serviceManager->get('pagebuilder\service\layout');
        $this->assertInstanceOf('PageBuilder\Service\LayoutService', $pageService);
    }

    public function testPageBuilder()
    {
        $pageService = $this->_serviceManager->get('viewhelpermanager')->get('buildpage');
        $this->assertInstanceOf('PageBuilder\View\Helper\PageBuilder', $pageService);
    }

    public function testGridInstance()
    {
        $grid = $this->_serviceManager->get('jqgrid');
        $this->assertInstanceOf('SynergyDataGrid\Grid\GridType\DoctrineORMGrid', $grid);
    }
}
