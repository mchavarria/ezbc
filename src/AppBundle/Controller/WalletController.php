<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Wallet;
use AppBundle\Form\WalletType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Unirest;

/**
 * Class WalletController
 * @Route("/app/wallet")
 */
class WalletController extends Controller
{
    const EXAMPLE_URL = 'https://ez-blockchain-middleware.herokuapp.com/etherium/testnet/getbalance/0x72b7d5dff8509f321a40e28e37f18b3198a26cbe';

    /**
     * @Template("@App/Wallet/index.html.twig")
     *
     * @Route("/index", name="app_wallet_index")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
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

//        $walletRepository = $this->getDoctrine()->getRepository(Wallet::class);
//        $wallets = $walletRepository->findBy([
//            'user' => $user->getId()
//        ]);

        $parameters = [
            'user' => $user
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
        $form = $this->createForm(WalletType::class, $wallet);

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

            return $this->redirectToRoute('app_user_index');
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

        $form = $this->createForm(WalletType::class, $wallet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wallet);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        $parameters = [
            'wallet' => $wallet,
            'form' => $form->createView()
        ];

        return $parameters;
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

        $resp = Unirest\Request::get(self::EXAMPLE_URL);
        $info = json_decode(json_encode($resp->body), true);

        $parameters = [
            'wallet' => $wallet,
            'info' => $info
        ];

        return $parameters;
    }
}
