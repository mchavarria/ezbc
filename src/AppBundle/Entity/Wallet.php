<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Wallet
 * @ORM\Entity
 * @ORM\Table(name="user_wallet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WalletRepository")
 */
class Wallet
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
    private $walletKey;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wallets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=FALSE)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $bcType;

    /**
     * BcPublicKey constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $key
     */
    public function setWalletKey($key)
    {
        $this->walletKey = $key;
    }

    /**
     * @return string
     */
    public function getWalletKey()
    {
        return $this->walletKey;
    }

    /**
     * @param string $type
     */
    public function setBcType($type)
    {
        $this->bcType = $type;
    }

    /**
     * @return string
     */
    public function getBcType()
    {
        return $this->bcType;
    }
}
