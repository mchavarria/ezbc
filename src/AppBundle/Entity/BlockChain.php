<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class BlockChain
 * @ORM\Entity
 * @ORM\Table(name="block_chain")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlockChainRepository")
 */
class BlockChain
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
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=30, nullable=FALSE)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=TRUE)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=TRUE)
     */
    private $explorer;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $url
     */
    public function setExplorer($url)
    {
        $this->explorer = $url;
    }

    /**
     * @return string
     */
    public function getExplorer()
    {
        return $this->explorer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
