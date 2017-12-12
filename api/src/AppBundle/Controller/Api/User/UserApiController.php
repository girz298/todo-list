<?php

namespace AppBundle\Controller\Api\User;

use AppBundle\Component\PrettyJsonResponse;
use AppBundle\Controller\Api\ApiController;
use AppBundle\Entity\User;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Class UserApiController
 * @package AppBundle\Controller\API\User
 */
class UserApiController extends ApiController
{
    /**
     * @Route("api/login", name="login")
     * @Method({"POST"})
     * @return Response
     */
    public function loginAction()
    {
        // TODO: Should be covered by tests
        /**@var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->get('security.authorization_checker');
        /**@var AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $errors = [
                'response' => true,
                'error' => $error->getMessage()
            ];
            return new PrettyJsonResponse($errors, 401);
        }

        if ($authorizationChecker->isGranted('ROLE_USER')) {
            return new PrettyJsonResponse([
                'response' => true,
                'status' => 'logged'
            ], 200);
        }

        return new PrettyJsonResponse([
            'response' => true,
            'error' => 'Login failed!'
        ], 400);
    }

    /**
     * @param Request $request
     * @Route("api/register", name="register")
     * @Method({"POST"})
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authorization_checker');
        if ($authenticationUtils->isGranted('ROLE_USER')) {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'You are already registered!'
            ], 409);
        }

        $user = new User();
        $user
            ->setUsername($request->get('username') ? $request->get('username') : '')
            ->setPassword($this
                ->get('security.password_encoder')
                ->encodePassword($user, $request->get('password') ? $request->get('password') : '')
            )
            ->setEmail($request->get('email') ? $request->get('email') : '');

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsArr = [
                'response' => true,
                'errors' => []
            ];
            /**@var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsArr['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return new PrettyJsonResponse($errorsArr, 400);
        } else {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($user);
                $em->flush();
            } catch (\Exception $exception) {
                /**@var PDOException $pdoException */
                $pdoException = $exception->getPrevious();
                if ($pdoException->getCode() == '23000' && $pdoException->getErrorCode() == 1062) {
                    return new PrettyJsonResponse([
                        'response' => true,
                        'error' => 'Fields "username" and "email" should be unique'
                    ], 409);

                }
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => $exception->getMessage(),
                ], 502);
            }
            return new PrettyJsonResponse([
                'response' => true,
                'status' => 'created'
            ], 201);
        }
    }
}