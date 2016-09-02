<?php
namespace PageBuilder\Model;

use Doctrine\ORM\QueryBuilder;
use PageBuilder\Entity\Setting;
use PageBuilder\Entity\Site;

/**
 * Class SiteModel
 *
 * @package PageBuilder\Model
 */
class SettingModel extends BaseModel
{
    /**
     * @param $site
     *
     * @return array
     */
    public function getSettingList(Site $site)
    {
        $settingList = [];
        $items       = $this->getSettingBySiteId($site->getId());
        /** @var $setting \PageBuilder\Entity\Setting */
        foreach ($items as $setting) {
            $code               = $setting->getSettingKey()->getCode();
            $value              = $setting->getSettingValue() ?: $setting->getSettingKey()->getDefaultValue();
            $settingList[$code] = $value;
        }

        $locale                = $site->getLocale() ?: 'en_GB';
        $settingList           = array_merge($settingList, \Locale::parseLocale($locale));
        $settingList['locale'] = $locale;

        return $settingList;
    }

    /**
     * @param $siteId
     *
     * @return array
     */
    public function getSettingBySiteId($siteId)
    {
        /** @var $query QueryBuilder */
        $params = ['dataSource' => $siteId];
        $qb     = $this->getFindByQueryBuilder($params, null, 'e');
        $qb->addSelect('k')
            ->innerJoin('e.settingKey', 'k');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $settingKeyId
     * @param array $params
     *
     * @return Setting
     */
    public function getSettingByKey($settingKeyId, array $params)
    {
        /** @var $query QueryBuilder */
        $params['settingKey'] = $settingKeyId;

        $qb = $this->getFindByQueryBuilder($params, null, 'e');

        return $qb->getQuery()->getOneOrNullResult();
    }
}
