<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @Route("/app/user")
 */
class UserController extends Controller
{
    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Template("@App/User/index.html.twig")
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
        $form = $this->createForm(UserType::class, $user);

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

        $form = $this->createForm(UserType::class, $user);

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
}
