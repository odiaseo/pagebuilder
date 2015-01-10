<?php
namespace PageBuilder\Model;

use Doctrine\ORM\QueryBuilder;
use PageBuilder\Entity\Site;

/**
 * Class SiteModel
 *
 * @package PageBuilder\Model
 */
class SettingModel extends BaseModel {
	/**
	 * @param $site
	 *
	 * @return array
	 */
	public function getSettingList(Site $site ) {
		$settingList = array();
		$items       = $this->getSettingBySiteId( $site->getId() );
		/** @var $setting \PageBuilder\Entity\Setting */
		foreach ( $items as $setting ) {
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
	 * @param $siteId
	 *
	 * @return array
	 */
	public function getSettingBySiteId( $siteId ) {
		/** @var $query QueryBuilder */
		$qb = $this->getFindByQueryBuilder( array( 'dataSource' => $siteId ), null, 'e' );
		$qb->addSelect( 'k' )
		   ->innerJoin( 'e.settingKey', 'k' );

		return $qb->getQuery()->getResult();
	}
}
