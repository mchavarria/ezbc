<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BcTransaction
 * @ORM\Entity
 * @ORM\Table(name="bc_transaction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcTransactionRepository")
 */
class BcTransaction
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $bcHash;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $hash
     */
    public function setBcHash($hash)
    {
        $this->bcHash = $hash;
    }

    /**
     * @return string
     */
    public function getBcHash()
    {
        return $this->bcHash;
    }
}