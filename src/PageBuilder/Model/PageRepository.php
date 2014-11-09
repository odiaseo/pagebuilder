<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\QueryException;
use SynergyCommon\Model\NestedSetRepository;
use SynergyCommon\ModelTrait\LocalAwareNestedSetTrait;

class PageRepository extends NestedSetRepository
{
    use LocalAwareNestedSetTrait;
}