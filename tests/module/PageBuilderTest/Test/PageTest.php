<?php
namespace PageBuilderTest\Test;

use PageBuilderTest\BaseTestClass;

/**
 * @backupGlobals disabled
 */
class PageTest extends BaseTestClass
{
    /** @var \SynergyDataGrid\Grid\GridType\DoctrineORMGrid; */
    protected $_grid;

    public function testService()
    {
        $pageService = $this->_serviceManager->get('pagebuilder\service\layout');
        $this->assertInstanceOf('PageBuilder\Service\LayoutService', $pageService);
    }

}