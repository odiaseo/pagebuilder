<?php
namespace PageBuilder\Model;

/**
 * Class SiteModel
 *
 * @package PageBuilder\Model
 */
class SiteModel extends BaseModel {
	public function getSettingList( $siteId ) {
		/** @var $site \PageBuilder\Entity\Site */
		$site        = $this->findObject( $siteId );
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

	public function findSiteByDomain( $domain ) {

	}
}
