<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class ResourceModel
 *
 * @package PageModel\Model
 */
class ResourceModel extends BaseModel
{
    use LocaleAwareTrait;

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
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e')
            ->from($this->_entity, 'e')
            ->where('e.isGeneric = 1')
            ->setMaxResults($limit)
            ->getQuery();

        $query = LocaleAwareTrait::addHints($query);
        $this->setEnableHydrationCache($this->enableResultCache);

        return $query->execute($mode);
    }
}
