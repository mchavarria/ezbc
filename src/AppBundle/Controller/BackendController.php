<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BcTransaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(BcTransaction::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $transactions = $repository->findBy([], ['createdDate' => 'DESC'], 10);
            $template = '@App/Backend/admin.html.twig';
        } else {
            $transactions = $repository->getAllByUser($this->getUser(), 10);
            $template = '@App/Backend/index.html.twig';
        }

        $parameters = [
            'bcTransactions' => $transactions
        ];

        return $this->render($template, $parameters);
    }
}
