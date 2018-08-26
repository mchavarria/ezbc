<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BcPublicKeyRepository
 */
class BcPublicKeyRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT bpk FROM AppBundle:BcPublicKey bpk ORDER BY bpk.id ASC'
            )
            ->getResult();
    }
}