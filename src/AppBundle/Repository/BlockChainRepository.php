<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BlockChainRepository
 */
class BlockChainRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT bc FROM AppBundle:BlockChain bc ORDER BY bc.name ASC'
            )
            ->getResult();
    }
}