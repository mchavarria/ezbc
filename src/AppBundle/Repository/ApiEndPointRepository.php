<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ApiEndPointRepository
 */
class ApiEndPointRepository extends EntityRepository
{
    public function countAllByUser($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('aep')
            ->from('AppBundle:ApiEndPoint', 'aep')
            ->where('aep.user = :user')
            ->setParameter('user', $id);

        $query = $qb->getQuery();

        return count($query->getResult());
    }
}
