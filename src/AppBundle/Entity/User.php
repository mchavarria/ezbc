<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=false)
 */
class User extends BaseUser
{
    use BlameableEntityTrait;
    use SoftDeleteableEntityTrait;
    use TimestampableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="Wallet", mappedBy="user", orphanRemoval=true)
     */
    private $wallets;

    /**
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $type;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->wallets = new ArrayCollection();
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastname
     */
    public function setLastName($lastname)
    {
        $this->lastName = $lastname;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return ArrayCollection
     */
    public function getWallets()
    {
        return $this->wallets;
    }

    /**
     * Add wallet.
     *
     * @param Wallet $wallet
     *
     * @return User
     */
    public function addWallet(Wallet $wallet)
    {
        $this->wallets[] = $wallet;

        return $this;
    }

    /**
     * Remove wallet.
     *
     * @param Wallet $wallet
     */
    public function removeWallet(Wallet $wallet)
    {
        $this->wallets->removeElement($wallet);
    }

    /**
     * Has zone property use.
     *
     * @param Wallet $wallet
     *
     * @return bool
     */
    public function hasWallet(Wallet $wallet)
    {
        return $this->wallets->contains($wallet);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->lastName.' '.$this->firstName;
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_'.strtoupper($this->type);

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

}
