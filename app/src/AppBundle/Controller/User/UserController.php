<?php
/**
 * Created by PhpStorm.
 * User: doctor
 * Date: 01.09.16
 * Time: 1:34
 */

namespace AppBundle\Controller\User;

use AppBundle\Entity\ForgotPassword;
use AppBundle\Form\User\ForgotPasswordType;
use AppBundle\Form\User\ResetPasswordType;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\User\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method({"GET"})
     * @return Response
     */
    public function indexAction()
    {
        $authenticationUtils = $this->get('security.authorization_checker');
        if ($authenticationUtils->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('task_create');
        }
        return $this->render('anon/index.html.twig');
    }

    /**
     * @param Post
     * @Route("/login", name="login")
     * @Method({"POST","GET"})
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authorization_checker');
        if ($authenticationUtils->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('task_create');
        }
        /**@var AuthenticationUtils $authenticationUtils*/
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $errors = [
                'success' => true,
                'error' => $error->getMessage()
            ];
            return new JsonResponse($errors,401);
        }
        $lastUsername = $authenticationUtils->getLastUsername();
//        return new JsonResponse($error);
        return $this->render(
            'anon/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @param Request $request
     * @Route("/register", name="register")
     * @Method({"GET","POST"})
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authorization_checker');
        if ($authenticationUtils->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('task_create');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $user = $form->getData();
            $user->setUsername($form->get('username')->getData());
            $encoder = $this->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword(
                $user,
                $form->get('password')->getData()
            ));
            $user->setIsActive(true);
            $user->setRole('ROLE_USER');
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('login');

        }

        return $this->render('anon/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $request
     * @Route(
     *     "/forgot_password",
     *     name="forgot_password"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();
            $user = $userRepo->findOneByEmail($email);
            if (!is_null($user)) {
                $forgotPassword = new ForgotPassword();
                $forgotPassword->setEmail($email);
                $hash = md5(uniqid(null, true));
                $forgotPassword->setHashedKey($hash);

                $message = \Swift_Message::newInstance()
                    ->setSubject('Hello Email')
                    ->setFrom('test298298@gmail.com')
                    ->setTo($email)
                    ->setBody('To reset you password <b>please</b> 
                    follow this link ' . $this->generateUrl('reset_password', ['hash' => $hash], 0));
                $this->get('mailer')->send($message);
                $em->persist($forgotPassword);
                $em->flush();
                $this->addFlash('notice', 'Email with instructions was send to you email!');
                return $this->redirectToRoute('login');
            } else {
                $this->addFlash('notice', 'User with that email not found!');
                return $this->redirectToRoute('forgot_password');
            }
        }
        return $this->render('non/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param $request
     * @param $hash
     * @Route(
     *     "/resetpassword/{hash}",
     *     name="reset_password"
     * )
     * @Method({"GET","POST"})
     * @return Response
     */
    public function resetPasswordAction(Request $request, $hash)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $forgotRepo = $em->getRepository('AppBundle:ForgotPassword');

        $forgotEntity = $forgotRepo->findOneByHashedKey($hash);
        if (!is_null($forgotEntity)) {
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $user = $userRepo->findOneByEmail($forgotEntity->getEmail());
                $encoder = $this->get('security.password_encoder');
                $user->setPassword($encoder->encodePassword(
                    $user,
                    $form->get('new_password')->getData()
                ));
                $em->remove($forgotEntity);
                $em->persist($user);
                $em->flush();
                $this->addFlash('notice', 'You are successfully reset your password');
                return $this->redirectToRoute('login');
            }
            return $this->render('anon/forgot_password.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('index');
        }
    }
}
