<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class BcTransaction
 * @ORM\Entity
 * @ORM\Table(name="bc_transaction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcTransactionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=false)
 */
class BcTransaction
{
    use BlameableEntityTrait;
    use SoftDeleteableEntityTrait;
    use TimestampableEntityTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=FALSE)
     */
    private $bcHash;

    /**
     * @ORM\ManyToOne(targetEntity="BcPublicKey")
     * @ORM\JoinColumn(name="bc_public_key_id", referencedColumnName="id", nullable=FALSE)
     */
    private $bcPublicKey;

    /**
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $bcType;

    /**
     * BcTransaction constructor.
     * @param BcPublicKey   $bcPublicKey
     * @param string        $type
     */
    public function __construct(BcPublicKey $bcPublicKey, $type)
    {
        $this->bcPublicKey = $bcPublicKey;
        $this->bcType = $type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BcPublicKey
     */
    public function getBcPublicKey()
    {
        return $this->bcPublicKey;
    }

    /**
     * @return string
     */
    public function getBcType()
    {
        return $this->bcType;
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
