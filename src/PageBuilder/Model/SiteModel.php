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
    const TYPE_PRODUCT = 2;

    /**
     * @param $site
     *
     * @return array
     */
    public function getSettingList(Site $site)
    {
        $settingList = [];
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
     * @param int $mode
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSiteBy(array $params, $mode = AbstractQuery::HYDRATE_OBJECT)
    {
        /** @var $query QueryBuilder */
        $qb = $this->getFindByQueryBuilder($params, null, 'e');
        $qb->addSelect('x,y,z,a,l')
            ->innerJoin('e.siteType', 'x')
            ->leftJoin('e.parent', 'z')
            ->leftJoin('e.subDomains', 'a')
            ->leftJoin('e.linkedSites', 'l')
            ->leftJoin('e.rootPage', 'y');

        if ($mode != AbstractQuery::HYDRATE_OBJECT) {
            $qb->setEnableHydrationCache($this->isEnableResultCache());
        }

        $site = $qb->getQuery()->getOneOrNullResult($mode);

        return $site;
    }

    /**
     * @param bool $voucherSites
     * @param int $mode
     * @param int $page
     * @param int | null $limit
     * @return array
     */
    public function getActiveVoucherSites($voucherSites = true, $mode = AbstractQuery::HYDRATE_ARRAY, $page = 1, $limit = null)
    {
        /** @var QueryBuilder $query */
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('partial e.{id,domain,isSubDomain,locale,displayTitle,isAdmin,voucherCount}')
            ->from($this->getEntity(), 'e')
            ->where('e.isActive = :active');

        if ($voucherSites) {
            $query->andWhere('e.siteType = :siteType');
            $type = self::TYPE_VOUCHER;
        } else {
            $query->andWhere('e.siteType = :siteType');
            $type = self::TYPE_PRODUCT;
        }

        if ($page and $limit) {
            $firstResult = ($page - 1) * $limit;
            $query->setFirstResult($firstResult)
                ->setMaxResults($limit);
        }

        $query->andWhere('e.isAdmin = :zero')
            ->setParameters(
                [
                    ':active'   => 1,
                    ':zero'     => 0,
                    ':siteType' => $type,
                ]
            );

        if ($mode == AbstractQuery::HYDRATE_ARRAY) {
            $query->setEnableHydrationCache($this->isEnableResultCache());
        }

        return $query->getQuery()->setHydrationMode($mode)->execute();
    }

    /**
     * @param int $mode
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
                [
                    ':active' => 1,
                ]
            );

        if ($siteType) {
            $query->andWhere('e.siteType = :siteType')
                ->setParameter(':siteType', $siteType);
        } else {
            $query->orderBy('e.siteType');
        }

        return $query->getQuery()->getResult($mode);
    }
}
