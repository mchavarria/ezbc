<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\BcTransaction;
use AppBundle\Entity\User;
use AppBundle\Form\ApiEndPointType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Route("/app/api-management")
 */
class ApiController extends Controller
{
    const CONSUME_EXAMPLE = 'https://ez-blockchain-middleware.herokuapp.com/ethereum/testnet/sendtransaction/%s/%s/%s/%s';
    //TODO cambiar ETHEREUM_WALLET_TO por configuracion.
    const ETHEREUM_WALLET_TO = '0xc095f4e5913dA8be66890b406C08BC13E3b2708D';

    /**
     * @Route("/index", name="app_api_management_index")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Template("@App/Api/index.html.twig")
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
     * @Route("/{id}/my", name="app_api_management_my", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Api/my.html.twig")
     *
     * @param int     $id      user id
     *
     * @return array
     */
    public function myApisAction($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

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
        $form = $this->createForm(ApiEndPointType::class, $endPoint, ['user' => $user]);

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
                $redirectUrl = $this->redirectToRoute('app_api_management_my', ['id' => $user->getId()]);
            }

            return $redirectUrl;
        }

        $parameters = [
            'aep' => $endPoint,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/edit/{id}/end-point", name="app_api_management_edit_end_point", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/Api/edit.html.twig")
     *
     * @param Request $request
     * @param integer $id
     *
     * TODO agregar role ADMIN nada mas
     *
     * @return array|Response
     */
    public function editAction(Request $request, $id)
    {

        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);
        $user = $this->getUser();

        $form = $this->createForm(ApiEndPointType::class, $aep, ['user' => $user]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($aep);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $redirectUrl = $this->redirectToRoute('app_api_management_index');
            } else {
                $redirectUrl = $this->redirectToRoute('app_api_management_my', ['id' => $user->getId()]);
            }

            return $redirectUrl;
        }

        $parameters = [
            'aep' => $aep,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/change-status", name="app_api_management_change_status", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function changeStatusAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);
        $enable = $aep->isEnabled() ? true : false;
        $aep->setEnabled(!$enable);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($aep);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_api_management_index');

        return $redirectUrl;
    }

    /**
     * @Route("/{id}/delete", name="app_api_management_delete", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function deleteAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(ApiEndPoint::class);
        $aep = $repository->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($aep);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_api_management_index');

        return $redirectUrl;
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

        $bcRepository = $this->getDoctrine()->getRepository(BcTransaction::class);
        $bcTransactions = $bcRepository->findby(['apiEndPoint' => $aep], [], 10);

        $parameters = [
            'aep' => $aep,
            'bcTransactions' => $bcTransactions
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
}
