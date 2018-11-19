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
        $result = $this->getEntityManager()
            ->createQuery(
                "SELECT aep FROM AppBundle:ApiEndPoint aep
                WHERE aep.user = '.$id.'"
            )
            ->getResult();

        return count($result);
    }
}
