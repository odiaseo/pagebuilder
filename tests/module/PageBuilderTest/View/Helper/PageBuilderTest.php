<?php
namespace PageBuilderTest\View\Helper;

use PageBuilder\BaseWidget;
use PageBuilder\Util\Widget;
use PageBuilder\View\Helper\PageBuilder;
use PageBuilderTest\Bootstrap;

/**
 * @backupGlobals disabled
 */
class PageBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testPageBuilderInstance()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $builder        = $serviceManager->get('ViewHelperManager')->get('pageBuilder');
        $this->assertInstanceOf(PageBuilder::class, $builder);
    }

    /**
     * @param $alias
     * @param $className
     *
     * @dataProvider widgetListProvider
     */
    public function testPageBuilderCanInstantiateWidgetsByName($alias, $className)
    {
        $serviceManager = Bootstrap::getServiceManager();
        /** @var PageBuilder $builder */
        $builder = $serviceManager->get('ViewHelperManager')->get('pageBuilder');

        /** @var BaseWidget $widget */
        $widget = $builder->getServiceLocator()->get($alias . 'widget');
        $this->assertInstanceOf($className, $widget);

        $text = $widget->translate('test');
        $this->assertInternalType('string', $text);

        $result = $widget->render();
        $this->assertInternalType('string', $result);
    }

    public function widgetListProvider()
    {
        /** @var Widget $util */
        $util   = Bootstrap::getServiceManager()->get('util\widget');
        $list   = $util->getRegistry();
        $return = [];

        foreach ($list as $id => $item) {
            $return[] = [$id, $item['class']];
        }

        return $return;
    }
}