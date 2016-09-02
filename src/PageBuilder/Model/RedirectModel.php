<?php
namespace PageBuilder\Model;

/**
 * Class RedirectModel
 *
 * @package Vaboose\Model
 */
class RedirectModel extends BaseModel
{

    const TYPE_EXACT_MATCH = 1;
    const TYPE_PREFIX_MATCH = 2;
    const TYPE_SUFFIX_MATCH = 3;

    /**
     * @return array
     */
    public function getAllRedirects()
    {
        $entity = $this->getEntity();
        $qb     = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e.source, e.redirectType, e.redirectDestination, s.id as sourceId, s.domain')
            ->from($entity, 'e')
            ->innerJoin('e.sites', 's');

        $qb->setEnableHydrationCache($this->enableResultCache);

        return $qb->getQuery()->getArrayResult();
    }
}
