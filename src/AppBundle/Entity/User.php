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
     * @ORM\OneToMany(targetEntity="BcPublicKey", mappedBy="user")
     */
    private $publicKeys;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->publicKeys = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @return ArrayCollection
     */
    public function getPublicKeys()
    {
        return $this->publicKeys;
    }
}