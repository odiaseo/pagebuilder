<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;

class SiteModel
    extends BaseModel
{
    public function getSettingList($siteId)
    {
        /** @var $site \PageBuilder\Entity\Site */
        $site = $this->findObject($siteId);

        $locale      = $site->getLocale() ? : 'en_GB';
        $settingList = \Locale::parseLocale($locale);

        /** @var $setting \PageBuilder\Entity\Setting */
        foreach ($site->getSettings() as $setting) {
            $code               = $setting->getSettingKey()->getCode();
            $value              = $setting->getSettingValue() ? : $setting->getSettingKey()->getDefaultValue();
            $settingList[$code] = $value;
        }

        return $settingList;
    }
}