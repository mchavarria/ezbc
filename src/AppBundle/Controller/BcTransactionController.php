<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BcTransaction;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class BcTransactionController
 *
 * @Route("/app/bc-transaction")
 */
class BcTransactionController extends Controller
{
    /**
     * @Route("/index", name="app_bc_transaction_index")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Template("@App/BcTransaction/index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(BcTransaction::class);
        $transactions = $repository->findAll();

        return [
            'bcTransactions' => $transactions
        ];
    }

    /**
     * @Route("/{id}/my", name="app_bc_transaction_my", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Template("@App/BcTransaction/my.html.twig")
     *
     * @param int     $id      user id
     *
     * @return array
     */
    public function myBcTransactionAction($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        $repository = $this->getDoctrine()->getRepository(BcTransaction::class);
        $transactions = $repository->getAllByUser($user);

        return [
            'bcTransactions' => $transactions
        ];
    }
}
