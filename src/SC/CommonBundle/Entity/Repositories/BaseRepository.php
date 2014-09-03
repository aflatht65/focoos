<?php
namespace SC\CommonBundle\Entity\Repositories;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository {
    protected $paginator;
    protected $localeExtension;
    protected $cache;

    /**
     * @DI\InjectParams({
     *     "paginator" = @DI\Inject("sc.paginator.extension")
     * })
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @DI\InjectParams({
     *     "cache" = @DI\Inject("cache")
     * })
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    protected function getResult(\Doctrine\ORM\QueryBuilder $qb, $paginate = false, $limitPerPage = null, $pageParam = 'page') {
        //$query = $qb->getQuery();
        //$query->useResultCache(true);
        if($paginate) {
            $paginator = $this->paginator->paginate($qb, $limitPerPage, $pageParam);
            return $paginator;
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    protected function getSumStats($startdate, $beforeperiod, array $afterperiod) {
        $sum = (int)$beforeperiod;
        // Remove potential order parameters from the date
        for($i = 0; $i < count($afterperiod); $i++) {
            if(($pos = strpos($afterperiod[$i]['dr'], ' ')) !== false) {
                $afterperiod[$i]['dr'] = substr($afterperiod[$i]['dr'], $pos+1);
            }
        }
        // Bug fix: Prevent having multiple stats on the first day
        usort($afterperiod, function($a, $b) {
            if ($a['dr'] == $b['dr']) {
              return 0;
            }
            return ($a['dr'] < $b['dr']) ? -1 : 1;
        });
        if(isset($afterperiod[0])) {
            while($afterperiod[0]['dr'] === $startdate) {
                $element = array_shift($afterperiod);
                $sum = $sum + (int)$element[1];
            }
        }

        // Initialize the results array
        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $startdate.' 00:00:00');
        $result = array(
            array($datetime->getTimestamp()*1000, $sum)
        );

        foreach($afterperiod as $curitem) {
            $sum = $sum + (int)$curitem[1];
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $curitem['dr'].' 00:00:00');
            if($datetime instanceof \DateTime) {
                $result[] = array($datetime->getTimestamp()*1000, $sum);
            }
        }
        /*usort($result, function($a, $b) {
            if($a[0] === $b[0]) {
                return 0;
            } else if($a[0] > $b[0]) {
                return 1;
            } else {
                return -1;
            }
        });*/
        return $result;
    }

    protected function parseStatOptions($alias, array $statoptions) {
        if(isset($statoptions['groupby']) && $statoptions['groupby'] != "") {
            $groupby = $statoptions['groupby'];
        } else {
            throw new \Exception('You need to select a field to group by');
        }
        if(isset($statoptions['period']) && isset($statoptions['period']['start']) && $statoptions['period']['start'] != "") {
            $periodstart = $statoptions['period']['start'];
        } else {
            $periodstart = new \DateTime('-1 month', new \DateTimeZone("UTC"));
            $periodstart = $periodstart->format('Y-m-d');
        }
        if(isset($statoptions['period']) && isset($statoptions['period']['end']) && $statoptions['period']['end'] != "") {
            $periodend = $statoptions['period']['end'];
        } else {
            $periodend = new \DateTime('now', new \DateTimeZone("UTC"));
            $periodend = $periodend->format('Y-m-d');
        }
        if(isset($statoptions['groupinterval']) && strtolower($statoptions['groupinterval']) === 'month') {
            $groupinterval = 'MONTH('.$alias.'.' . $groupby . ')';
        } else if(isset($statoptions['groupinterval']) && strtolower($statoptions['groupinterval']) === 'week') {
            $groupinterval = 'WEEK('.$alias.'.' . $groupby . ')';
        } else if(isset($statoptions['groupinterval']) && strtolower($statoptions['groupinterval']) === 'dayofweek') {
            $groupinterval = 'DAYOFWEEK('.$alias.'.' . $groupby . ')';
        } else {
            $groupinterval = 'DATE('.$alias.'.' . $groupby . ')';
        }
        if(isset($statoptions['onlysum']) && $statoptions['onlysum'] == true) {
            $onlysum = true;
        } else {
            $onlysum = false;
        }
        return array(
            $groupby,
            $periodstart,
            $periodend,
            $groupinterval,
            $onlysum
        );
    }

    protected function getStats($onlysum, $periodstart, $qb, $qb2) {
        if($onlysum == false) {
            $stats = array();
            $stats['absolute'] = $this->getSumStats($periodstart, $qb->getQuery()->getSingleScalarResult(), $qb2->getQuery()->getArrayResult());
            $stats['relative'] = $qb2->getQuery()->getArrayResult();
            return $stats;
        } else {
            return $this->getSumStats($periodstart, $qb->getQuery()->getSingleScalarResult(), $qb2->getQuery()->getArrayResult());
        }
    }
}
?>