<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Activity;
use AppBundle\PseudoEntity\WeeklyActivity;
use Doctrine\ORM\EntityRepository;

/**
 * ActivityRepository
 */
class ActivityRepository extends EntityRepository
{
    /**
     * @param string[] $filters
     * @param string[] $sort
     * @return Activity[]
     */
    public function findByFilters(array $filters, array $sort = array())
    {
        $qb = $this->createQueryBuilder('a');

        foreach ($filters as $name => $filter) {
            if (!$filter) {
                continue;
            }
            switch ($name) {
                case "user";
                    $qb->andWhere("a.user = :user")->setParameter("user", $filter);
                    break;
                case "from";
                    $qb->andWhere("a.day >= :from")->setParameter("from", new \DateTime($filter));
                    break;
                case "to";
                    $qb->andWhere("a.day <= :to")->setParameter("to", new \DateTime($filter));
                    break;
            }
        }

        foreach ($sort as $field => $direction) {
            if ($this->getClassMetadata()->hasField($field)) {
                $qb->addOrderBy("a." . $field, $direction);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string[] $filters
     * @return Activity[]
     */
    public function findByFiltersWeekGroup(array $filters)
    {
        $conn = $this->getEntityManager()->getConnection();
        $qb = $conn->createQueryBuilder()
            ->addSelect("YEARWEEK(a.day) as week")
            ->addSelect("AVG(a.distance/a.time) as a")
            ->from("activity AS a");

        foreach ($filters as $name => $filter) {
            if (!$filter) {
                continue;
            }
            switch ($name) {
                case "user";
                    $qb->andWhere("a.user_id = :user")->setParameter("user", $filter->getId());
                    break;
                case "from";
                    $qb->andWhere("a.day >= :from")->setParameter("from", $filter);
                    break;
                case "to";
                    $qb->andWhere("a.day <= :to")->setParameter("to", $filter);
                    break;
            }
        }
        $qb->orderBy('YEARWEEK(day)');
        $qb->addGroupBy("YEARWEEK(day)");

        $res = $qb->execute();

        $return = [];
        foreach ($res as $row) {
            $w = new WeeklyActivity();

            $w->setSpeed($row['a']);
            $w->setWeek(substr($row['week'], 4));
            $w->setYear(substr($row['week'], 0, 4));

            $return[] = $w;
        }

        return $return;
    }
}
