<?php

namespace PageBuilderTest\Entity;

use PageBuilderTest\Bootstrap;

/**
 * Class run generic tests on entites. Verifies simple getters/setters
 */
class GenericModelTest extends \PHPUnit\Framework\TestCase
{

    protected $serviceManager;
    protected $stack = [];

    public function setUp(): void
    {
        $this->stack = [
            'site',
            'component',
            'redirect',
            'resource',
            'section',
            'setting-key',
            'site-rank',
            'site-type',
            'template',
            'theme'
        ];

        $this->serviceManager = Bootstrap::getServicemanager();
    }

    public function testModelInstance()
    {

        foreach ($this->stack as $m) {
            /** @var $model \SynergyCommon\Model\AbstractModel */
            $model = $this->serviceManager->get('pagebuilder\model\\' . $m);
            $this->assertInstanceOf('SynergyCommon\Model\AbstractModel', $model);
            $entityClass = $model->getEntity();

            /** @var $entity \SynergyCommon\Entity\AbstractEntity */
            $entity = new $entityClass;
            $this->assertInstanceOf('SynergyCommon\Entity\AbstractEntity', $entity);

            $data = array();
            $entity->exchangeArray($data);
        }
    }
}
