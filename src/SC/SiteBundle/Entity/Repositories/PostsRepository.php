<?php

namespace SC\SiteBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SC\UserBundle\Wantlet\ORM\Point;

class PostsRepository extends EntityRepository
{
    public function findPosts(array $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from($this->_entityName, 'p');

        $this->addFilters($qb, $filters);

        return $qb->getQuery()->getResult();
    }

    protected function addFilters(\Doctrine\ORM\QueryBuilder &$qb, array $filters) {
        $qb->join('p.lesson', 'l');
        if(isset($filters['lessons']) && is_array($filters['lessons'])) {
            $qb->andWhere('l.id IN(:lessons)');
            $qb->setParameter('lessons', $filters['lessons']);
        }
        if(isset($filters['deadlineAfter']) && $filters['deadlineAfter'] instanceof \DateTime) {
            $qb->andWhere('p.deadlineDate >= :deadlineAfter');
            $qb->setParameter('deadlineAfter', $filters['deadlineAfter']);
        }
        if(isset($filters['deadlineBefore']) && $filters['deadlineBefore'] instanceof \DateTime) {
            $qb->andWhere('p.deadlineDate <= :deadlineBefore');
            $qb->setParameter('deadlineBefore', $filters['deadlineBefore']);
        }
        $qb->orderBy('p.createdAt', 'DESC');
    }
}
