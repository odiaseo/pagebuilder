<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use SynergyCommon\Model\TranslatableModelTrait;
use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class ResourceModel
 *
 * @package PageModel\Model
 */
class ResourceModel extends BaseModel
{
    use LocaleAwareTrait;
    use TranslatableModelTrait;

    /**
     * List Generic Resources
     *
     * @param int $limit
     * @param int $mode
     *
     * @return mixed
     */
    public function getGenericResources($limit = 10, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder->select('e')
            ->from($this->_entity, 'e')
            ->where('e.isGeneric = 1')
            ->setMaxResults($limit);

        $builder->setEnableHydrationCache($this->enableResultCache);
        $query = LocaleAwareTrait::addHints($builder->getQuery());

        return $query->execute($mode);
    }
}
