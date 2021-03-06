<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 *
 * @Route("/app/user")
 */
class UserController extends Controller
{
    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * UserController constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Template("@App/User/index.html.twig")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Route("/index", name="app_user_index")
     *
     * @return array
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return [
            'users' => $users
        ];
    }

    /**
     * @Route("/new", name="app_user_new")
     *
     * @Template("@App/User/new.html.twig")
     *
     * @param Request $request
     *
     * @return array|Response
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_new' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            //$user = $form->getData();
            $plainPassword = $user->getPlainPassword();
            $encoded = $this->encoder->encodePassword($user, $plainPassword);

            $user->setPassword($encoded);


            $user->setEnabled(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $redirectUrl = $this->redirectToRoute('app_user_index');
            } else {
                $redirectUrl = $this->redirectToRoute('backend_dashboard');
            }

            return $redirectUrl;
        }

        $parameters = [
            'user' => $user,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Template("@App/User/edit.html.twig")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function editAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        $form = $this->createForm(UserType::class, $user, ['is_new' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $redirectUrl = $this->redirectToRoute('app_user_index');
            } else {
                $redirectUrl = $this->redirectToRoute('backend_dashboard');
            }

            return $redirectUrl;
        }

        $parameters = [
            'user' => $user,
            'form' => $form->createView()
        ];

        return $parameters;
    }

    /**
     * @Route("/{id}/change-status", name="app_user_change_status", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function changeStatusAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
        $enable = $user->isEnabled() ? true : false;
        $user->setEnabled(!$enable);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_user_index');

        return $redirectUrl;
    }

    /**
     * @Route("/{id}/delete", name="app_user_delete", requirements={"id" = "\d+"}, options={"expose" = true})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request HTTP request.
     * @param int     $id      Identifier
     *
     * @return array|Response
     */
    public function deleteAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $redirectUrl = $this->redirectToRoute('app_user_index');

        return $redirectUrl;
    }
}
