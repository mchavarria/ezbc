<?php

namespace AppBundle\Controller;

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
        return [];
    }
}
