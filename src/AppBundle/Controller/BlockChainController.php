<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlockChain;
use AppBundle\Form\BlockChainType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlockChainController
 * @Route("/adm/bc")
 */
class BlockChainController extends Controller
{
    /**
     * @Template("@App/BlockChain/index.html.twig")
     *
     * @Route("/index", name="adm_bc_index")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(BlockChain::class);
        $blockChains = $repository->findAll();

        return [
            'blockChains' => $blockChains
        ];
    }

    /**
     * @Route("/new", name="adm_bc_new", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/BlockChain/new.html.twig")
     *
     * @param Request $request HTTP request.
     *
     * @return array|Response
     */
    public function newAction(Request $request)
    {
        $blockChain = new BlockChain();
        $form = $this->createForm(BlockChainType::class, $blockChain);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blockChain);
            $entityManager->flush();

            return $this->redirectToRoute('adm_bc_index');
        }

        $parameters = [
            'blockChain' => $blockChain,
            'form' => $form->createView()
        ];

        return $parameters;
    }


    /**
     * @Route("/{id}/edit", name="adm_bc_edit", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/BlockChain/edit.html.twig")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function editAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(BlockChain::class);
        $blockChain = $repository->find($id);

        $form = $this->createForm(BlockChainType::class, $blockChain);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blockChain);
            $entityManager->flush();

            return $this->redirectToRoute('adm_bc_index');
        }

        $parameters = [
            'blockChain' => $blockChain,
            'form' => $form->createView()
        ];

        return $parameters;
    }
}
