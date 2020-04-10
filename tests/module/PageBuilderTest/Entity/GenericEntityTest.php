<?php

namespace PageBuilderTest\Entity;

use PageBuilder\Entity\BaseEntity;
use PageBuilder\Entity\Component;
use PageBuilder\Entity\Join\PageTemplate;
use PageBuilder\Entity\Join\SiteTheme;
use PageBuilder\Entity\Join\TemplateSection;
use PageBuilder\Entity\Licence;
use PageBuilder\Entity\Module;
use PageBuilder\Entity\Page;
use PageBuilder\Entity\Redirect;
use PageBuilder\Entity\Resource;
use PageBuilder\Entity\ResourceType;
use PageBuilder\Entity\Section;
use PageBuilder\Entity\Setting;
use PageBuilder\Entity\SettingKey;
use PageBuilder\Entity\Site;
use PageBuilder\Entity\SiteRank;
use PageBuilder\Entity\SiteType;
use PageBuilder\Entity\Template;
use PageBuilder\Entity\Theme;
use PageBuilderTest\Bootstrap;
use Laminas\Filter\StringTrim;

/**
 * Class run generic tests on entites. Verifies simple getters/setters
 */
class GenericEntityTest extends \PHPUnit\Framework\TestCase
{
    protected $serviceManager;
    protected $stack = [];

    public function setUp()
    {
        $this->stack = [
            BaseEntity::class,
            Component::class,
            Page::class,
            Redirect::class,
            Resource::class,
            Section::class,
            Setting::class,
            SettingKey::class,
            Site::class,
            Theme::class,
            SiteRank::class,
            SiteType::class,
            Template::class,
            PageTemplate::class,
            SiteTheme::class,
            TemplateSection::class,
            ResourceType::class,
        ];

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testAbstractEntityFactory()
    {
        $config = [
            'component'        => 'PageBuilder\Entity\Component',
            'page'             => 'PageBuilder\Entity\Page',
            'section'          => 'PageBuilder\Entity\Section',
            'site-rank'        => 'PageBuilder\Entity\SiteRank',
            'site'             => 'PageBuilder\Entity\Site',
            'template'         => 'PageBuilder\Entity\Template',
            'theme'            => 'PageBuilder\Entity\Theme',
            'setting'          => 'PageBuilder\Entity\Setting',
            'redirect'         => 'PageBuilder\Entity\Redirect',
            'page-template'    => 'PageBuilder\Entity\Join\PageTemplate',
            'site-theme'       => 'PageBuilder\Entity\Join\SiteTheme',
            'template-section' => 'PageBuilder\Entity\Join\TemplateSection',
        ];

        /** @var $sampleService \SynergyCommon\Service\BaseService */
        $sampleService = $this->serviceManager->get('synergycommon\service\base');

        foreach ($config as $key => $class) {
            $entityClassName = $sampleService->getClassnameFromEntityKey($key);
            $this->assertSame($class, $entityClassName);
        }
    }

    /**
     * @group entity
     */
    public function testEntities()
    {
        foreach ($this->stack as $declaredClass) {
            $split = explode('\\', $declaredClass);
            if (in_array('Entity', $split)) {
                $reflectionClass = new \ReflectionClass($declaredClass);

                if ($reflectionClass->IsInstantiable()) {
                    $class      = new $declaredClass;
                    $methods    = $reflectionClass->getMethods();
                    $methodList = [];
                    /** @var \ReflectionMethod $method */
                    foreach ($methods as $method) {
                        $methodName   = $method->getName();
                        $methodParams = $method->getParameters();

                        if (preg_match('/^set/', $methodName)) {
                            $attr               = lcfirst(substr($methodName, 3));
                            $methodList [$attr] = 'testdata ';
                            if (count($methodParams) === 1) {
                                /** @var \ReflectionParameter $param */
                                $param = current($methodParams);
                                if ($param->allowsNull()) {
                                    $class->$methodName(null);
                                    $this->assertTrue(true);
                                }
                            }
                        }

                        if (preg_match('/^(get|is)/', $methodName)) {
                            $this->assertTrue(true);
                            $class->$methodName('test');
                        }
                    }

                    /** @var BaseEntity $entity */
                    $entity = new $declaredClass();
                    $entity->exchangeArray($methodList);
                    $arrayList = $entity->toArray();
                    $this->assertTrue(is_array($arrayList));

                    if (method_exists($entity, 'ensureNoLineBreaks')) {
                        $entity->ensureNoLineBreaks();
                    }

                    $this->assertTrue(method_exists($entity, 'getId'));
                }
            }
        }
    }

    public function testMagicMethods()
    {
        $entity = new Page();
        $entity->getInputFilter()->add(
            [
                'name'    => 'slug',
                'filters' => [
                    ['name' => StringTrim::class]
                ]
            ]
        );

        $data = [
            'lft'   => 1,
            'rgt'   => 2,
            'level' => 1,
            'slug'  => 'page'
        ];

        $entity->fromArray($data);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $entity->$key);
        }
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider entityDataProvider
     */
    public function testToString($data, $expected)
    {
        $entity = new Page();
        $entity->fromArray($data);
        $this->assertSame($expected, (string)$entity);
    }

    /**
     * @param $test
     * @param $result
     * @dataProvider sampleDataProvider
     */
    public function testRemoveWhiteSpace($test, $result)
    {
        $entity = new Page();
        $this->assertSame($result, $entity->removeWhiteSpace($test));
    }

    public function sampleDataProvider()
    {
        return [
            ['test' . PHP_EOL, 'test'],
            ["\r\ntest", 'test'],
            ["test \n school", 'test school'],
        ];
    }

    public function entityDataProvider()
    {
        return [
            [['slug' => 'page'], 'page'],
            [['title' => 'test-title'], 'test-title'],
        ];
    }
}
