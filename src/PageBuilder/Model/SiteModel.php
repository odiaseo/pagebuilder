<?php
namespace PageBuilder\Model;

use Doctrine\ORM\QueryBuilder;
use PageBuilder\Entity\Site;

/**
 * Class SiteModel
 *
 * @package PageBuilder\Model
 */
class SiteModel extends BaseModel {
	/**
	 * @param $site
	 *
	 * @return array
	 */
	public function getSettingList( Site $site ) {
		$settingList = array();
		/** @var $setting \PageBuilder\Entity\Setting */
		foreach ( $site->getSettings() as $setting ) {
			$code                 = $setting->getSettingKey()->getCode();
			$value                = $setting->getSettingValue() ?: $setting->getSettingKey()->getDefaultValue();
			$settingList[ $code ] = $value;
		}

		$locale                = $site->getLocale() ?: 'en_GB';
		$settingList           = array_merge( $settingList, \Locale::parseLocale( $locale ) );
		$settingList['locale'] = $locale;

		return $settingList;
	}


	/**
	 * @param array $params
	 *
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findSiteBy( array $params ) {
		/** @var $query QueryBuilder */
		$qb    = $this->getFindByQueryBuilder( $params, null, 'e' );
		$query = $qb->addSelect( array( 'x, y' ) )
		            ->innerJoin( 'e.siteType', 'x' )
		            ->leftJoin( 'e.rootPage', 'y' )
		            ->setMaxResults( 1 );

		$site = $query->getQuery()->getOneOrNullResult();

		return $site;
	}
}
