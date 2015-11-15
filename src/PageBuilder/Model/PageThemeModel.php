<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use PageBuilder\Entity\Join\PageTheme;

/**
 * Class PageThemeModel
 *
 * @package PageBuilder\Model
 */
class PageThemeModel extends BaseModel
{
    /**
     * @param $id
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPageThemeById($id, $mode = AbstractQuery::HYDRATE_OBJECT)
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

        return $query->getOneOrNullResult($mode);
    }

    /**
     * @param     $id
     * @param int $mode
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPageThemeByThemeId($id, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */

        $query = $qb->select('e')
            ->from($this->_entity, 'e')
            ->where('e.themeId = :id')
            ->setMaxResults(1)
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult($mode);
    }

    /**
     * @param      $pageId
     * @param      $themeId
     * @param null $siteId
     * @param int  $mode
     *
     * @return PageTheme
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function getActivePageThemeForSite($pageId, $themeId, $siteId = null, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        /** @var $query \Doctrine\ORM\QueryBuilder */
        $qb     = $this->getEntityManager()->createQueryBuilder();
        $params = array(
            ':pageId'      => $pageId,
            ':active'      => 1,
            ':siteThemeId' => $themeId
        );
        $query  = $qb->select('e, p, t')
            ->from($this->_entity, 'e')
            ->innerJoin('e.themeId', 't')
            ->innerJoin('e.pageId', 'p')
            ->where('e.pageId = :pageId')
            ->setParameters($params);

        if (is_numeric($themeId)) {
            $query->andWhere('e.themeId = :siteThemeId');
        } else {
            $query->andWhere('t.slug = :siteThemeId');
        }

        $query->andWhere('e.isActive = :active');

        if ($siteId) {
       //     $query->andWhere($query->expr()->eq('e.dataSource', $siteId));
        }

        $query->setMaxResults(1);
        $result = $query->getQuery()->getOneOrNullResult($mode);

        return $result;
    }
}
