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

    const TYPE_VOUCHER = 1;

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
        $query = $qb->addSelect(array('x, y, z, a, l'))
            ->innerJoin('e.siteType', 'x')
            ->leftJoin('e.parent', 'z')
            ->leftJoin('e.subDomains', 'a')
            ->leftJoin('e.linkedSites', 'l')
            ->leftJoin('e.rootPage', 'y')
            ->setMaxResults(1);

        if ($mode != AbstractQuery::HYDRATE_OBJECT) {
            $query->setEnableHydrationCache($this->enableResultCache);
        }
        $site = $query->getQuery()->getOneOrNullResult($mode);

        return $site;
    }

    /**
     * @param bool $voucherSites
     *
     * @return array
     */
    public function getActiveVoucherSites($voucherSites = true)
    {
        /** @var QueryBuilder $query */
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id, e.domain, e.isSubDomain, e.locale, e.displayTitle')
            ->from($this->getEntity(), 'e')
            ->where('e.isActive = :active');

        if ($voucherSites) {
            $query->andWhere('e.siteType = :siteType');
        } else {
            $query->andWhere('e.siteType <> :siteType');
        }

        $query->andWhere('e.isAdmin = :zero')
            ->setParameters(
                array(
                    ':active'   => 1,
                    ':zero'     => 0,
                    ':siteType' => self::TYPE_VOUCHER
                )
            );
        $query->setEnableHydrationCache(true);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param int  $modewr
     * @param null $siteType
     *
     * @return array
     */
    public function getActiveDomains($mode = AbstractQuery::HYDRATE_OBJECT, $siteType = null)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e')
            ->from($this->getEntity(), 'e')
            ->where('e.isActive = :active')
            ->setParameters(
                array(
                    ':active' => 1,
                )
            );

        if ($siteType) {
            $query->andWhere('e.siteType = :siteType')
                ->setParameter(':siteType', $siteType);
        }

        return $query->getQuery()->getResult($mode);
    }
}
