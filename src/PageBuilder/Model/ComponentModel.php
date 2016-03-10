<?php
namespace PageBuilder\Model;

use SynergyCommon\Model\TranslatableModelTrait;
use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class ComponentModel
 *
 * @package PageBuilder\Model
 */
class ComponentModel extends BaseModel
{
    use LocaleAwareTrait;
    use TranslatableModelTrait;

    public function getShoppingGuides()
    {
        return $this->getGuidesWithPrefix('shopping-guide');
    }

    public function getFilterGuides()
    {
        return $this->getGuidesWithPrefix('filter-guide');
    }

    private function getGuidesWithPrefix($prefix)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $query   = $builder->select('e.content')
            ->from($this->getEntity(), 'e')
            ->where($builder->expr()->like('e.slug', ':regex'))
            ->setParameter(':regex', $prefix . '-%');

        $builder->setEnableHydrationCache($this->enableResultCache);
        $query = $this->addHints($query->getQuery());

        return $query->getScalarResult();
    }
}
