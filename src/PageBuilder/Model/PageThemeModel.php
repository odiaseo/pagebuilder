<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;

class PageThemeModel extends BaseModel
{
    public function getPageThemeById($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e, m')
            ->from($this->_entity, 'e')
            ->innerJoin('e.pageId', 'm')
            ->where('e.id = :id')
            ->setMaxResults(1)
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getPageThemeByThemeId($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where('e.themeId = :id')
            ->setMaxResults(1)
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }
}