<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class BcTransaction
 * @ORM\Entity
 * @ORM\Table(name="bc_transaction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcTransactionRepository")
 *
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
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(name="bc_wallet_key_id", referencedColumnName="id", nullable=FALSE)
     */
    private $wallet;

    /**
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $bcType;


    /**
     * @ORM\ManyToOne(targetEntity="ApiEndPoint", inversedBy="transactions")
     * @ORM\JoinColumn(name="api_ep_id", referencedColumnName="id", nullable=FALSE)
     */
    private $apiEndPoint;

    /**
     * BcTransaction constructor.
     * @param ApiEndPoint $apiEndPoint
     */
    public function __construct(ApiEndPoint $apiEndPoint)
    {
        $wallet = $apiEndPoint->getWallet();
        $this->apiEndPoint = $apiEndPoint;
        $this->wallet = $wallet;
        $this->bcType = $wallet->getBcType();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Wallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @return string
     */
    public function getBcType()
    {
        return $this->bcType;
    }

    /**
     * @return ApiEndPoint
     */
    public function getApiEndPoint()
    {
        return $this->apiEndPoint;
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
