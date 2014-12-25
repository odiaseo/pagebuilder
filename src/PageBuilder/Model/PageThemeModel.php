<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;

/**
 * Class PageThemeModel
 *
 * @package PageBuilder\Model
 */
class PageThemeModel extends BaseModel {
	/**
	 * @param $id
	 *
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getPageThemeById( $id, $mode = AbstractQuery::HYDRATE_OBJECT ) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		/** @var $query \Doctrine\ORM\Query */

		$query = $qb->select( 'e, m' )
		            ->from( $this->_entity, 'e' )
		            ->innerJoin( 'e.pageId', 'm' )
		            ->where( 'e.id = :id' )
		            ->setMaxResults( 1 )
		            ->setParameter( 'id', $id )
		            ->getQuery();

		return $query->getOneOrNullResult( $mode );
	}

	/**
	 * @param     $id
	 * @param int $mode
	 *
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getPageThemeByThemeId( $id, $mode = AbstractQuery::HYDRATE_OBJECT ) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		/** @var $query \Doctrine\ORM\Query */

		$query = $qb->select( 'e' )
		            ->from( $this->_entity, 'e' )
		            ->where( 'e.themeId = :id' )
		            ->setMaxResults( 1 )
		            ->setParameter( 'id', $id )
		            ->getQuery();

		return $query->getOneOrNullResult( $mode );
	}

	/**
	 * @param     $pageId
	 * @param     $siteTheme
	 * @param int $mode
	 *
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getActivePageThemeForSite( $pageId, $siteTheme, $mode = AbstractQuery::HYDRATE_OBJECT ) {
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

		$result = $query->getOneOrNullResult( $mode );

		return $result;
	}
}
