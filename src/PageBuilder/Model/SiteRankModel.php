<?php
namespace PageBuilder\Model;

use DateTime;

/**
 * Class SiteRankModel
 * @package PageBuilder\Model
 */
class SiteRankModel extends BaseModel
{
    /**
     * @param int $siteId
     * @param $fromDate
     * @param $toDate
     * @param $filter
     *
     * @return array
     */
    public function getReportQuery($siteId, $fromDate, $toDate, $filter)
    {
        $filter = strtolower($filter);

        if (empty($fromDate)) {
            $fromDate = (new DateTime('first day of the year'))->format('Y-m-d');
        }

        if (empty($toDate)) {
            $toDate = (new DateTime('last day of this month'))->format('Y-m-d');
        }

        $builder = $this->getEntityManager()->createQuerybuilder();
        $query   = $builder->select(
            [
                'e.popularity total',
                'DATE(e.rankedAt) regDay',
                's.locale domain',
                '1 valid',
                'e.rankedAt',
            ]
        )->from($this->getEntity(), 'e')
            ->innerJoin('e.site', 's')
            ->where('e.rankedAt >= :start')
            ->andWhere('e.rankedAt <= :end')
            ->andWhere('s.siteType = :siteType')
            ->addOrderBy('e.popularity')
            ->addOrderBy('e.rankedAt')
            ->addGroupBy('s.domain')
            ->setParameters(
                [
                    ':start'    => $fromDate,
                    ':end'      => $toDate,
                    ':siteType' => 1
                ]
            )->setMaxResults(10);

        if ($siteId) {
            $query->andWhere('e.site = :site')
                ->setParameter(':site', $siteId);
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
