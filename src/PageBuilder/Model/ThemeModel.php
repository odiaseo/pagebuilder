<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;

class ThemeModel extends BaseModel
{
    public function getActiveTheme($siteId)
    {
        $em    = $this->getEntityManager();
        $query = $em
            ->createQueryBuilder()
            ->select('t,s')
            ->from($this->_entity, 't')
            ->innerJoin('t.siteThemes', 's')
            ->where('s.siteId = :id')
            ->andWhere('s.isActive = 1')
            ->setParameter(':id', $siteId)
            ->getQuery();

        try {
            return $query->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (NoResultException $e) {
            return false;
        }
    }
}