<?php
namespace PageBuilder\Model;

use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class ComponentModel
 *
 * @package PageBuilder\Model
 */
class ComponentModel extends BaseModel
{
    use LocaleAwareTrait;

    public function getShoppingGuides()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->select('e.content')
            ->from($this->getEntity(), 'e')
            ->where($builder->expr()->like('e.slug', ':regex'))
            ->setParameter(':regex', 'shopping-guide-%');

        $query = $this->addHints($query->getQuery());
        $this->setEnableHydrationCache(true);

        return $query->getScalarResult();
    }
}
