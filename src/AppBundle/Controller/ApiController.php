<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\User;
use AppBundle\Form\ApiEndPointType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @Route("/app/api-management")
 */
class ApiController extends Controller
{
    /**
     * @Template("@App/Api/index.html.twig")
     *
     * @Route("/index", name="app_api_management_index")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $apiEndPoints = $repository->findAll();

        return [
            'apiEndPoints' => $apiEndPoints
        ];
    }

    /**
     * @Template("@App/Api/index.html.twig")
     *
     * @Route("/my", name="app_api_management_my")
     *
     * @return array
     */
    public function myApisAction()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $apiEndPoints = $repository->findBy(['user' => $user]);

        return [
            'apiEndPoints' => $apiEndPoints
        ];
    }

    /**
     * @Route("/new/end-point", name="app_api_management_new_end_point")
     *
     * @Template("@App/Api/new.html.twig")
     *
     * @param Request $request
     *
     * @return array|Response
     */
    public function newAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!count($user->getWallets())) {
            //TODO agregar mensaje de crear primero una wallet.
            return $this->redirect($this->generateUrl('app_wallet_my', ['id' => $user->getId()]));
        }

        $endPoint = new ApiEndPoint();
        $isAdmin = $this->isGranted('ROLE_ADMIN', $this->getUser());
        if (!$isAdmin) {
            $endPoint->setUser($user);
        }
        $form = $this->createForm(ApiEndPointType::class, $endPoint, ['is_admin' => $isAdmin]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            //$user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($endPoint);
            $entityManager->flush();

            if ($isAdmin) {
                $redirectUrl = $this->redirectToRoute('app_api_management_index');
            } else {
                $redirectUrl = $this->redirectToRoute('backend_dashboard');
            }

            return $redirectUrl;
        }

        $parameters = [
            'user' => $user,
            'apiEndPoint' => $endPoint,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Api:delete.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/{id}/details", name="app_api_management_details", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Api/details.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function detailAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);

        $parameters = [
            'endPoint' => $aep
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/stats", name="app_api_management_stats", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Api/stats.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function statsAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);

        $parameters = [
            'aep' => $aep
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/consume", name="app_api_consume", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @param integer $id
     *
     * @return array
     */
    public function consumeAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);

        $parameters = [
            'aep' => $aep
        ];

        return $parameters;
    }

}
