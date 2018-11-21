<?php

namespace AppBundle\Controller;

use AppBundle\Data\MiddleWareApi;
use AppBundle\Entity\User;
use AppBundle\Entity\Wallet;
use AppBundle\Form\WalletType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Unirest;

/**
 * Class WalletController
 *
 * @Route("/app/wallet")
 */
class WalletController extends Controller
{
    const EXAMPLE_URL = 'https://ez-blockchain-middleware.herokuapp.com/%s/%s/getbalance/%s';

    /**
     * @Template("@App/Wallet/index.html.twig")
     *
     * @Route("/index", name="app_wallet_index")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Wallet::class);
        $wallets = $repository->findAll();

        return [
            'wallets' => $wallets
        ];
    }

    /**
     * @Route("/{id}/my", name="app_wallet_my", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Wallet/my.html.twig")
     *
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function userWalletAction($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        $parameters = [
            'wallets' => $user->getWallets()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/new", name="app_wallet_new", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Wallet/new.html.twig")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function newAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        $wallet = new Wallet($user);
        $form = $this->createForm(WalletType::class, $wallet, ['user' => $user]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            //$user = $form->getData();
            $user->addWallet($wallet);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wallet);
            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $redirectUrl = $this->redirectToRoute('app_wallet_index');
            } else {
                $redirectUrl = $this->redirectToRoute('app_wallet_my', ['id' => $user->getId()]);
            }

            return $redirectUrl;
        }

        $parameters = [
            'wallet' => $wallet,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/edit", name="app_wallet_edit", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Wallet/edit.html.twig")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function editAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Wallet::class);
        $wallet = $repository->find($id);
        $user = $wallet->getUser();

        $form = $this->createForm(WalletType::class, $wallet, ['user' => $user]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wallet);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $redirectUrl = $this->redirectToRoute('app_wallet_index');
            } else {
                $redirectUrl = $this->redirectToRoute('app_wallet_my', ['id' => $user->getId()]);
            }

            return $redirectUrl;
        }

        $parameters = [
            'wallet' => $wallet,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/change-status", name="app_wallet_change_status", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function changeStatusAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Wallet::class);
        $wallet = $repository->find($id);
        $enable = $wallet->isEnabled() ? true : false;
        $wallet->setEnabled(!$enable);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($wallet);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_wallet_index');

        return $redirectUrl;
    }

    /**
     * @Route("/{id}/delete", name="app_wallet_delete", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function deleteAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Wallet::class);
        $wallet = $repository->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($wallet);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_wallet_index');

        return $redirectUrl;
    }

    /**
     * @Route("/{id}/info", name="app_wallet_info", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Wallet/info.html.twig")
     *
     * @param int     $id      Identifier
     *
     * @return array
     */
    public function infoAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Wallet::class);
        $wallet = $repository->find($id);

        $url = MiddleWareApi::METHOD_GET_WALLET_INFO;
        $url = sprintf(
            $url,
            $wallet->getBcType(),
            $wallet->getBcMode(),
            $wallet->getWalletKey()
        );
        $resp = Unirest\Request::get($url);
        $info = json_decode(json_encode($resp->body), true);
        //TODO improve code with HTTP response codes.
        //https://www.restapitutorial.com/httpstatuscodes.html
        $hasError = !(is_array($info));
        $parameters = [
            'wallet' => $wallet,
            'info' => $info,
            'hasError' => $hasError
        ];

        return $parameters;
    }
}
