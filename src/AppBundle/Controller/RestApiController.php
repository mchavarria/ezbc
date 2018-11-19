<?php

namespace AppBundle\Controller;

use AppBundle\Data\BcModes;
use AppBundle\Data\EzWallet;
use AppBundle\Data\MiddleWareApi;
use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\BcTransaction;
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
            return new JsonResponse([], 500);
        }

        //Estado de la API.
        if (!$aep->isEnabled()) {
            return new JsonResponse([], 500);
        }

        //Estado de la Wallet.
        $wallet = $aep->getWallet();
        if (!$wallet->isEnabled()) {
            return new JsonResponse([], 500);
        }

        //Saldo de la Wallet.
        if (!$this->checkBalance($wallet)) {
            return new JsonResponse([], 500);
        }

        $url = $this->getConsumeUrl($aep, $hash);
        if (!$url) {
            return new JsonResponse([], 500);
        }

        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);
        $hasError = !(is_array($info));

        if (!$hasError) {
            $txid = $info['txId'];

            //save Log.
            //TODO usar event dispatcher.
            //TODO solo txId si no hay error?
            $log = new BcTransaction($aep);
            $log->setBcHash($txid);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($log);
            $entityManager->persist($aep);
            $entityManager->flush();

            return new JsonResponse(['txid' => $txid]);
        }

            return new JsonResponse([], 500);
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

        $url = MiddleWareApi::METHOD_SEND_TRANSACTION;
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
     * @param ApiEndPoint $aep
     *
     * @return bool
     */
    protected function checkBalance(ApiEndPoint $aep)
    {
        $wallet = $aep->getWallet();

        $url = MiddleWareApi::METHOD_GET_BALANCE;
        $url = sprintf(
            $url,
            $wallet->getBcType(),
            $wallet->getBcMode(),
            $wallet->getWalletKey()
        );

        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);

        $hasError = !(is_array($info));
        if ($hasError) {
            return false;
        }

        $balance = (float) $info['balance'];

        if ($balance <= 0) {
            return false;
        }

        return true;
    }
}
