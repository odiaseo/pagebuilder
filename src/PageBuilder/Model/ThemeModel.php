<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Class ThemeModel
 *
 * @package PageBuilder\Model
 */
class ThemeModel extends BaseModel
{
    /**
     * @param $siteId
     *
     * @return bool|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActiveTheme($siteId)
    {
        $manager = $this->getEntityManager();
        $query   = $manager
            ->createQueryBuilder()
            ->select('t,s')
            ->from($this->_entity, 't')
            ->innerJoin('t.siteThemes', 's')
            ->where('s.siteId = :id')
            ->andWhere('s.isActive = 1')
            ->setParameter(':id', $siteId)
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (NoResultException $e) {
            return false;
        } catch (NonUniqueResultException $e) {
            return false;
        }
    }
}
