<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\QueryException;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use SynergyCommon\ModelTrait\LocalAwareNestedSetTrait;

class PageRepository extends NestedTreeRepository
{
    use LocalAwareNestedSetTrait;
}