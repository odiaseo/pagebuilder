<?php
namespace PageBuilder\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use SynergyCommon\Model\NestedSetRepositoryTrait;
use SynergyCommon\ModelTrait\LocalAwareNestedSetTrait;

/**
 * Class PageRepository
 *
 * @package PageBuilder\Model
 */
class PageRepository extends NestedTreeRepository
{
    use LocalAwareNestedSetTrait;
    use NestedSetRepositoryTrait;

    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $enabled = (APPLICATION_ENV == 'production');
        $this->setEnableResultCache($enabled);
    }
}
