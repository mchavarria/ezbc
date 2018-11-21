<?php

namespace AppBundle\Twig;

use AppBundle\Data\BcTxError;
use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\BlockChain;
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
            new \Twig_SimpleFunction('getApisQty', [$this, 'getApisQty']),
            new \Twig_SimpleFunction('getBcLogs', [$this, 'getBcLogs']),
            new \Twig_SimpleFunction('getHumanError', [$this, 'getHumanError']),
            new \Twig_SimpleFunction('getMoreInfoLink', [$this, 'getMoreInfoLink'])
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
        $qty = $repository->countAllByUser($user);

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
     * @param BcTransaction $log
     *
     * @return string
     */
    public function getMoreInfoLink(BcTransaction $log)
    {
        $blockChain = $log->getWallet()->getBcType();
        if ($blockChain->getExplorer()) {
            return $blockChain->getExplorer().$log->getBcHash();
        }

        return '#';
    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getHumanError($code)
    {
        if ($code == BcTxError::USER_DISABLED) {
            $msg = 'bct.item.error.user.disabled';
        } elseif ($code == BcTxError::API_DISABLED) {
            $msg = 'bct.item.error.api.disabled';
        } elseif ($code == BcTxError::WALLET_DISABLED) {
            $msg = 'bct.item.error.wallet.disabled';
        } elseif ($code == BcTxError::WALLET_INSUFFICIENT_FUNDS) {
            $msg = 'bct.item.error.wallet.insufficient.funds';
        } elseif ($code == BcTxError::WALLET_CONFIG) {
            $msg = 'bct.item.error.wallet.config';
        } else {
            $msg = 'bct.item.error.generic';
        }

        return $msg;
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