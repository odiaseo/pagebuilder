<?php
namespace PageBuilder\Model;

use SynergyCommon\Doctrine\QueryBuilder;

/**
 * Class RedirectModel
 *
 * @package Vaboose\Model
 */
class RedirectModel extends BaseModel
{

    const TYPE_EXACT_MATCH  = 1;
    const TYPE_PREFIX_MATCH = 2;
    const TYPE_SUFFIX_MATCH = 3;

    /**
     * @return array
     */
    public function getAllRedirects()
    {
        $entity = $this->getEntity();
        $qb     = $this->getEntityManager()->createQueryBuilder();
        /** @var QueryBuilder $query */
        $query = $qb->select('e.source, e.redirectType, e.redirectDestination, s.id as sourceId, s.domain')
            ->from($entity, 'e')
            ->innerJoin('e.sites', 's');

        $query->setEnableHydrationCache($this->enableResultCache);

        return $query->getQuery()->getArrayResult();
    }
}
