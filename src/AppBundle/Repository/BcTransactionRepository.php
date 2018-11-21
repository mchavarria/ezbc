<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class BcTransactionRepository
 */
class BcTransactionRepository extends EntityRepository
{
    /**
     * Get Total transactions by user
     *
     * @param User $user
     *
     * @return int
     */
    public function countAllByUser(User $user)
    {
        $result = $this->getAllByUser($user);

        return count($result);
    }

    /**
     * Get All transactions by User
     *
     * @param User $user
     * @param int  $limit
     *
     * @return array
     */
    public function getAllByUser(User $user, $limit = 100)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('bct')
            ->from('AppBundle:BcTransaction', 'bct')
            ->innerJoin('bct.apiEndPoint', 'aep', 'WITH', 'aep.user = :user')
            ->setMaxResults($limit)
            ->orderBy('bct.createdDate', 'DESC')
            ->setParameter('user', $user);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}