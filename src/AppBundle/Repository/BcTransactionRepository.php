<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BcTransactionRepository
 */
class BcTransactionRepository extends EntityRepository
{
    public function countAllByUser($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('bct')
            ->from('AppBundle:BcTransaction', 'bct')
            ->innerJoin('bct.apiEndPoint', 'aep', 'WITH', 'aep.user = :user AND aep.id = bct.apiEndPoint')
            ->setParameter('user', $id);

        $query = $qb->getQuery();

        return count($query->getResult());
    }
}