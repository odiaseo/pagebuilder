<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;

class SiteThemeModel extends BaseModel {

	/**
	 * Get Active Site Theme
	 *
	 * @param $siteId
	 *
	 * @return \PageBuilder\Entity\Join\SiteTheme
	 */
	public function getActiveSiteTheme( $siteId ) {
		$qb = $this->getEntityManager()->createQueryBuilder();
		/** @var $query \Doctrine\ORM\Query */

		$query = $qb->select( 'e, t' )
		            ->from( $this->_entity, 'e' )
		            ->innerJoin( 'e.themeId', 't' )
		            ->where( 'e.siteId = :id' )
		            ->andWhere( 'e.isActive = 1' )
		            ->setMaxResults( 1 )
		            ->setParameter( 'id', $siteId )
		            ->getQuery();

		/** @var $result \PageBuilder\Entity\Join\SiteTheme */
		$result = $query->getOneOrNullResult( AbstractQuery::HYDRATE_OBJECT );
		if ( $result ) {
			return $result->getThemeId();
		} else {
			return $result;
		}
	}
}
