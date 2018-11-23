<?php

namespace AppBundle\Controller;

use AppBundle\Data\BcTxError;
use AppBundle\Data\EzWallet;
use AppBundle\Data\MiddleWareApi;
use AppBundle\Data\OpCodes;
use AppBundle\Data\PlanFees;
use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\BcTransaction;
use AppBundle\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Unirest;

/**
 * Class RestApiController
 *
 * @Route("/rest-api")
 */
class RestApiController extends Controller
{
    /**
     * @Route("/v1/consume/{id}", name="app_rest_api_consume", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @param Request $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function consumeAction(Request $request, $id)
    {
        $hash = $request->query->get('hash');
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);
        $user = $aep->getUser();

        //Estado de Usuario.
        if (!$user->isEnabled()) {
            $this->saveLog($aep, BcTxError::USER_DISABLED, false);

            return new JsonResponse(['msg' => 'Error User Disabled'], OpCodes::ERROR);
        }

        //Estado de la API.
        if (!$aep->isEnabled()) {
            $this->saveLog($aep, BcTxError::API_DISABLED, false);

            return new JsonResponse(['msg' => 'Error API Disabled'], OpCodes::ERROR);
        }

        //Estado de la Wallet.
        $wallet = $aep->getWallet();
        if (!$wallet->isEnabled()) {
            $this->saveLog($aep, BcTxError::WALLET_DISABLED, false);

            return new JsonResponse(['msg' => 'Error Wallet Disabled'], OpCodes::ERROR);
        }

        //Saldo de la Wallet.
        if (!$this->checkBalance($wallet)) {
            $this->saveLog($aep, BcTxError::WALLET_INSUFFICIENT_FUNDS, false);

            return new JsonResponse(['msg' => 'Error Insufficient Funds on Wallet'], OpCodes::ERROR);
        }

        $url = $this->getConsumeUrl($aep, $hash);
        if (!$url) {
            $this->saveLog($aep, BcTxError::WALLET_CONFIG, false);

            return new JsonResponse(['msg' => 'Error on Wallet Configuration'], OpCodes::ERROR);
        }

        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);
        $hasError = !(is_array($info));
        $code = (int) $resp->code;

        if ($code == 200 && !$hasError) {
            $txid = $info['txId'];

            //save Log.
            //TODO usar event dispatcher.
            $this->saveLog($aep, $txid);

            return new JsonResponse(['txid' => $txid]);
        }

        $this->saveLog($aep, BcTxError::TX_GENERIC_ERROR, false);

        return new JsonResponse(['msg' => 'Error on blockchain'], OpCodes::ERROR);
    }

    /**
     * @param ApiEndPoint $aep
     * @param mixed       $content
     * @param bool        $successful
     */
    protected function saveLog($aep, $content, $successful = true)
    {
        $log = new BcTransaction($aep);

        $log->setSuccessful($successful);
        if ($successful) {
            $log->setBcHash($content);
        } else {
            $log->setErrorCode($content);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($log);
        $entityManager->persist($aep);
        $entityManager->flush();
    }

    /**
     * Generate URL to consume.
     *
     * @param ApiEndPoint $aep
     * @param string      $hash
     *
     * @return bool|string
     */
    protected function getConsumeUrl(ApiEndPoint $aep, $hash)
    {
        $wallet = $aep->getWallet();

        $walletName = strtoupper($wallet->getBcType().'_'.$wallet->getBcMode());

        if (!EzWallet::isValidName($walletName)) {
            return false;
        }

        $wallets = EzWallet::getConstants();
        $walletTo = $wallets[$walletName];

        $userType = strtoupper($aep->getUser()->getType());
        if (!PlanFees::isValidName($userType)) {
            return false;
        }

        $fees = PlanFees::getConstants();
        $fee = (float) $fees[$userType];

        $url = MiddleWareApi::METHOD_SEND_TRANSACTION;
        //TODO API cuando reciba fee
//        $url = sprintf(
//            $url,
//            $wallet->getBcType(),
//            $wallet->getBcMode(),
//            $walletTo,
//            $wallet->getWalletKey(),
//            $wallet->getPKey(),
//            $hash,
//            $fee
//        );
        //API sin fee.
        $url = sprintf(
            $url,
            $wallet->getBcType(),
            $wallet->getBcMode(),
            $walletTo,
            $wallet->getWalletKey(),
            $wallet->getPKey(),
            $hash
        );

        return $url;
    }

    /**
     * Verifica si la wallet tiene saldo.
     *
     * @param Wallet $wallet
     *
     * @return bool
     */
    protected function checkBalance(Wallet $wallet)
    {
        $url = MiddleWareApi::METHOD_GET_BALANCE;
        $url = sprintf(
            $url,
            $wallet->getBcType(),
            $wallet->getBcMode(),
            $wallet->getWalletKey()
        );

        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);

        $code = (int) $resp->code;

        if ($code != 200) {
            return false;
        }

        $balance = (float) $info['balance'];

        if ($balance <= 0) {
            return false;
        }

        return true;
    }
}
