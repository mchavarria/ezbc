<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class WalletRepository
 */
class WalletRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT w FROM AppBundle:Wallet w ORDER BY w.id ASC'
            )
            ->getResult();
    }
}