<?php

namespace AppBundle\Twig;

use AppBundle\Data\BcTxError;
use AppBundle\Data\ExplorerLinks;
use AppBundle\Data\EzWallet;
use AppBundle\Data\MiddleWareApi;
use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\BlockChain;
use AppBundle\Entity\User;
use AppBundle\Entity\BcTransaction;
use AppBundle\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Unirest;

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
            new \Twig_SimpleFunction('getBlockChainsQty', [$this, 'getBlockChainsQty']),
            new \Twig_SimpleFunction('getBcTxLogs', [$this, 'getBcTxLogs']),
            new \Twig_SimpleFunction('getDbWalletMoreInfoLink', [$this, 'getDbWalletMoreInfoLink']),
            new \Twig_SimpleFunction('getHumanError', [$this, 'getHumanError']),
            new \Twig_SimpleFunction('getTxMoreInfoLink', [$this, 'getTxMoreInfoLink']),
            new \Twig_SimpleFunction('getUsersQty', [$this, 'getUsersQty']),
            new \Twig_SimpleFunction('getWalletBalance', [$this, 'getWalletBalance']),
            new \Twig_SimpleFunction('getWalletsQty', [$this, 'getWalletsQty']),
            new \Twig_SimpleFunction('getWalletMoreInfoLink', [$this, 'getWalletMoreInfoLink'])
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
     * @param User|null $user
     *
     * @return int
     */
    public function getBcTxLogs(User $user = null)
    {
        $repository = $this->em->getRepository(BcTransaction::class);
        if ($user) {
            $qty = $repository->countAllByUser($user);
        } else {
            $qty = count($repository->findAll());
        }

        return $qty;
    }

    /**
     * @param User|null $user
     *
     * @return int
     */
    public function getApisQty(User $user = null)
    {
        $repository = $this->em->getRepository(ApiEndPoint::class);
        if ($user) {
            $qty = $repository->countAllByUser($user->getId());
        } else {
            $qty = count($repository->findAll());
        }

        return $qty;
    }

    /**
     * @param string $bcType
     * @param string $bcMode
     *
     * @return float
     */
    public function getWalletBalance($bcType, $bcMode)
    {
        $walletName = strtoupper($bcType.'_'.$bcMode);

        if (!EzWallet::isValidName($walletName)) {
            return 0;
        }

        $wallets = EzWallet::getConstants();
        $walletTo = $wallets[$walletName];

        $url = MiddleWareApi::METHOD_GET_BALANCE;
        $url = sprintf(
            $url,
            $bcType,
            $bcMode,
            $walletTo
        );

        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);

        $hasError = !(is_array($info));
        $code = (int) $resp->code;

        if ($code == 200 && !$hasError) {
            $balance = (float) $info['balance'];

            return $balance;
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getWalletsQty()
    {
        $repository = $this->em->getRepository(Wallet::class);
        $qty = count($repository->findAll());

        return $qty;
    }

    /**
     * @return int
     */
    public function getBlockChainsQty()
    {
        $repository = $this->em->getRepository(BlockChain::class);
        $qty = count($repository->findAll());

        return $qty;
    }

    /**
     * @return int
     */
    public function getUsersQty()
    {
        $repository = $this->em->getRepository(User::class);
        $qty = count($repository->findAll());

        return $qty;
    }

    /**
     * @param BcTransaction $log
     *
     * @return string
     */
    public function getTxMoreInfoLink(BcTransaction $log)
    {
        $blockChain = $log->getWallet()->getBcType();
        if ($blockChain->getExplorer()) {
            return $blockChain->getExplorer().ExplorerLinks::TX_INFO_URL.$log->getBcHash();
        }

        return '#';
    }

    /**
     * @param Wallet $wallet
     *
     * @return string
     */
    public function getWalletMoreInfoLink(Wallet $wallet)
    {
        $blockChain = $wallet->getBcType();
        if ($blockChain->getExplorer()) {
            return $blockChain->getExplorer().ExplorerLinks::ADDRESS_INFO_URL.$wallet->getWalletKey();
        }

        return '#';
    }

    /**
     * @param string $bcType
     * @param string $bcMode
     *
     * @return string
     */
    public function getDbWalletMoreInfoLink($bcType, $bcMode)
    {
        $walletName = strtoupper($bcType.'_'.$bcMode);

        if (!EzWallet::isValidName($walletName)) {
            return '#';
        }

        $wallets = EzWallet::getConstants();
        $walletTo = $wallets[$walletName];

        $repository = $this->em->getRepository(BlockChain::class);
        $blockChain = $repository->findOneBy(['code' => $bcType]);

        if ($blockChain && $blockChain->getExplorer()) {
            return $blockChain->getExplorer().ExplorerLinks::ADDRESS_INFO_URL.$walletTo;
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