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

/**
 * Class WalletController
 * @Route("/app/wallet")
 */
class WalletController extends Controller
{
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
}
