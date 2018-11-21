<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BcTransaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BackendController
 *
 * @Route("/app")
 */
class BackendController extends Controller
{
    /**
     * @Route("/index", name="backend_dashboard")
     *
     * @Template("@App/Backend/index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(BcTransaction::class);
        $transactions = $repository->getAllByUser($this->getUser(), 10);

        return [
            'bcTransactions' => $transactions
        ];
    }
}
