<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApiEndPoint
 *
 * @ORM\Table(name="api_end_point")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiEndPointRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=false)
 */
class ApiEndPoint
{
    use BlameableEntityTrait;
    use SoftDeleteableEntityTrait;
    use TimestampableEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=70)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="apiEndPoints")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=FALSE)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Wallet")
     */
    private $wallet;

    /**
     * @ORM\OneToMany(targetEntity="BcTransaction", mappedBy="apiEndPoint", orphanRemoval=true)
     */
    private $transactions;


    /**
     * ApiEndPoint constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ApiEndPoint
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ApiEndPoint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param User $user
     *
     * @return ApiEndPoint
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Wallet $wallet
     *
     * @return ApiEndPoint
     */
    public function setWallet(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Get wallet
     *
     * @return Wallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }


    /**
     * @return ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add transaction.
     *
     * @param BcTransaction $transaction
     *
     * @return ApiEndPoint
     */
    public function addTransaction(BcTransaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction.
     *
     * @param BcTransaction $transaction
     */
    public function removeTransaction(BcTransaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     *
     * @param BcTransaction $transaction
     *
     * @return bool
     */
    public function hasTransaction(BcTransaction $transaction)
    {
        return $this->transactions->contains($transaction);
    }
}

