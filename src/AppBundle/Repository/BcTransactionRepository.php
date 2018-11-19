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
        $result = $this->getEntityManager()
            ->createQuery(
                "SELECT bct FROM AppBundle:BcTransaction bct
                INNER JOIN AppBundle:ApiEndPoint aep WITH aep.id IN 
                (SELECT aap.id FROM AppBundle:ApiEndPoint aap where aep.user = '.$id.')"
            )
            ->getResult();

        return count($result);
    }
}