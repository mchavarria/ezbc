<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BcTransactionRepository
 */
class BcTransactionRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM AppBundle:BcTransaction t ORDER BY p.id ASC'
            )
            ->getResult();
    }
}