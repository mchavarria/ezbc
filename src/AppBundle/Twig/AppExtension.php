<?php

namespace AppBundle\Twig;

use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\User;
use AppBundle\Entity\BcTransaction;
use Doctrine\ORM\EntityManagerInterface;

class AppExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AppExtension constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('ordinal', [$this, 'ordinal'])
        ];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('testFunction', [$this, 'testFunction']),
            new \Twig_SimpleFunction('getBcLogs', [$this, 'getBcLogs']),
            new \Twig_SimpleFunction('getApisQty', [$this, 'getApisQty'])
        ];
    }

    /**
     * @param int $number
     *
     * @return string
     */
    public function ordinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            $str = $number.'th';
        } else {
            $str = $number.$ends[$number % 10];
        }

        return $str.' Renewal';
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getBcLogs(User $user)
    {
        $repository = $this->em->getRepository(BcTransaction::class);
        $qty = $repository->countAllByUser($user->getId());

        return $qty;
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getApisQty(User $user)
    {
        $repository = $this->em->getRepository(ApiEndPoint::class);
        $qty = $repository->countAllByUser($user->getId());

        return $qty;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function testFunction($name)
    {
        return 'Hola '.$name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}