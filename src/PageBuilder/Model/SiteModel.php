<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;
use PageBuilder\Entity\Site;
use SynergyCommon\Doctrine\QueryBuilder;

/**
 * Class SiteModel
 *
 * @package PageBuilder\Model
 */
class SiteModel extends BaseModel
{
    /**
     * @param $site
     *
     * @return array
     */
    public function getSettingList(Site $site)
    {
        $settingList = array();
        /** @var $setting \PageBuilder\Entity\Setting */
        foreach ($site->getSettings() as $setting) {
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
     * @param array $params
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSiteBy(array $params, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        /** @var $query QueryBuilder */
        $qb    = $this->getFindByQueryBuilder($params, null, 'e');
        $query = $qb->addSelect(array('x, y, z'))
            ->innerJoin('e.siteType', 'x')
            ->leftJoin('e.parent', 'z')
            ->leftJoin('e.rootPage', 'y')
            ->setMaxResults(1);

        if ($mode != AbstractQuery::HYDRATE_OBJECT) {
            $query->setEnableHydrationCache($this->enableResultCache);
        }
        $site = $query->getQuery()->getOneOrNullResult($mode);

        return $site;
    }
}
