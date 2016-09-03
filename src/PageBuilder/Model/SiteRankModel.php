<?php
namespace PageBuilder\Model;

use DateTime;

/**
 * Class SiteRankModel
 *
 * @package PageBuilder\Model
 */
class SiteRankModel extends BaseModel
{
    /**
     * @param int $siteId
     * @param $fromDate
     * @param $toDate
     * @param $filter
     * @param int $limit
     *
     * @return array
     */
    public function getReportQuery($siteId, $fromDate, $toDate, $filter, $limit = null)
    {
        $siteList = [];
        $filter   = strtolower($filter);

        if (empty($fromDate)) {
            $fromDate = (new DateTime('first day of the year'))->format('Y-m-d');
        }

        if (empty($toDate)) {
            $toDate = (new DateTime('last day of this month'))->format('Y-m-d');
        }

        $builder = $this->getEntityManager()->createQuerybuilder();
        $query   = $builder->select(
            [
                '(e.popularity) total',
                'DATE(e.rankedAt) regDay',
                's.domain domain',
                '1 valid',
                'e.rankedAt',
            ]
        )->from($this->getEntity(), 'e')
            ->innerJoin('e.site', 's')
            ->where('e.rankedAt >= :start')
            ->andWhere('e.rankedAt <= :end')
            ->andWhere('s.siteType = :siteType')
            ->addOrderBy('e.rankedAt')
            ->addOrderBy('e.popularity')
            ->addGroupBy('e.site')
            ->setParameters(
                [
                    ':start'    => $fromDate,
                    ':end'      => $toDate,
                    ':siteType' => 1,
                ]
            );

        if ($siteId) {
            $siteList = array_filter(array_unique(array_merge($siteList, (array)$siteId)));
        } elseif ($limit) {
            $subQueryBuilder = $builder = $this->getEntityManager()->createQuerybuilder();
            $subQuery        = $subQueryBuilder->select('f.id, s.id site_id')
                ->from($this->getEntity(), 'f')
                ->innerJoin('f.site', 's')
                ->groupBy('s.id')
                ->orderBy('f.popularity', 'ASC')
                ->setMaxResults($limit);

            foreach ($subQuery->getQuery()->getArrayResult() as $item) {
                $siteList[] = $item['site_id'];
            }
        }

        if ($siteList) {
            $query->andWhere($builder->expr()->in('s.id', $siteList));
        }
        switch ($filter) {
            case 'year':
                $query->addSelect('YEAR(e.rankedAt) regYear')
                    ->addGroupBy('regYear');
                break;
            case 'month':
                $query->addSelect('Month(e.rankedAt) regMonth')
                    ->addGroupBy('regMonth');
                break;
            case 'week':
                $query->addSelect('WEEK(e.rankedAt) regWeek')
                    ->addGroupBy('regWeek');
                break;
            case 'hour':
                $query->addSelect('HOUR(e.rankedAt) regHour')
                    ->addGroupBy('regHour');
                break;
            case 'day':
            default:
                $query->addGroupBy('regDay');
        }

        return $query->getQuery()->getArrayResult();
    }
}
