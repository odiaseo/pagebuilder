<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use PageBuilder\Entity\Join\PageTheme;

class PageThemeModel extends BaseModel {
	public function getPageThemeById( $id ) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		/** @var $query \Doctrine\ORM\Query */

		$query = $qb->select( 'e, m' )
		            ->from( $this->_entity, 'e' )
		            ->innerJoin( 'e.pageId', 'm' )
		            ->where( 'e.id = :id' )
		            ->setMaxResults( 1 )
		            ->setParameter( 'id', $id )
		            ->getQuery();

		return $query->getOneOrNullResult( AbstractQuery::HYDRATE_OBJECT );
	}

	public function getPageThemeByThemeId( $id ) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		/** @var $query \Doctrine\ORM\Query */

		$query = $qb->select( 'e' )
		            ->from( $this->_entity, 'e' )
		            ->where( 'e.themeId = :id' )
		            ->setMaxResults( 1 )
		            ->setParameter( 'id', $id )
		            ->getQuery();

		return $query->getOneOrNullResult( AbstractQuery::HYDRATE_OBJECT );
	}

	/**
	 * @param $pageId
	 * @param $siteTheme
	 *
	 * @return PageTheme
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getActivePageThemeForSite( $pageId, $siteTheme ) {
		/** @var $query \Doctrine\ORM\Query */
		$qb    = $this->getEntityManager()->createQueryBuilder();
		$query = $qb->select( 'e, t' )
		            ->from( $this->_entity, 'e' )
		            ->innerJoin( 'e.themeId', 't' )
		            ->where( 'e.pageId = :pageId' )
		            ->andWhere( 't.slug = :siteTheme' )
		            ->andWhere( 'e.isActive = :active' )
		            ->setParameters(
			            array(
				            ':pageId'    => $pageId,
				            ':active'    => 1,
				            ':siteTheme' => $qb->expr()->literal( $siteTheme )
			            )
		            )->getQuery();

		$result = $query->getOneOrNullResult();

		return $result;
	}
}
