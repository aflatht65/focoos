<?php

namespace SC\UserBundle\Entity\Repositories;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class PrivateMessageRepository extends EntityRepository
{
    public function findPrivateMessages(array $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from($this->_entityName, 'p');

        $this->addFilters($qb, $filters);

        return $qb->getQuery()->getResult();
    }

    public function findLastPrivateMessagesByReceiver($user) {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->_entityName, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'createdAt', 'createdAt');
        $rsm->addFieldResult('a', 'message', 'message');
        $rsm->addJoinedEntityResult('SC\UserBundle\Entity\User', 'u1', 'a', 'sender');
        $rsm->addJoinedEntityResult('SC\UserBundle\Entity\User', 'u2', 'a', 'receiver');
        $rsm->addFieldResult('u1', 'sender_id', 'id');
        $rsm->addFieldResult('u2', 'receiver_id', 'id');

        $query = $this->_em->createNativeQuery('SELECT a.id, a.sender_id, a.receiver_id, a.createdAt, a.isRead, a.message FROM privatemessage a INNER JOIN (SELECT  sender_id, receiver_id, MAX(createdAt) cdate FROM privatemessage GROUP BY sender_id, receiver_id) b ON a.sender_id = b.sender_id AND a.receiver_id = b.receiver_id AND a.createdAt = b.cdate WHERE a.receiver_id = ? ORDER BY a.createdAt DESC', $rsm);
        $query->setParameter(1, $user->getId());

        return $query->getResult();
    }

    protected function addFilters(\Doctrine\ORM\QueryBuilder &$qb, array $filters) {
        if(isset($filters['sender']) && $filters['sender'] != '') {
            $qb->andWhere('p.sender = :sender');
            $qb->setParameter('sender', $filters['sender']);
        }
        if(isset($filters['receiver']) && $filters['receiver'] != '') {
            $qb->andWhere('p.receiver = :receiver');
            $qb->setParameter('receiver', $filters['receiver']);
        }
        if(isset($filters['between']) && $filters['between'] != '') {
            $qb->andWhere('p.receiver = :breceiver OR p.sender = :breceiver OR p.receiver = :bsender OR p.sender = :bsender');
            $qb->setParameter('breceiver', $filters['between'][0]);
            $qb->setParameter('bsender', $filters['between'][1]);
        }
        $qb->orderBy('p.createdAt', 'DESC');
    }
}
