<?php

namespace AppBundle\Controller\Api;

use AppBundle\Component\PrettyJsonResponse;
use AppBundle\Entity\User;
use AppBundle\Form\Api\AuthenticateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


/**
 * Class ApiController
 * @package AppBundle\Controller\API
 */
class ApiController extends Controller
{
    /**
     * @Route("/api", name="api_main")
     * @Method({"GET"})
     * @return Response
     */
    public function apiEntryPointAction()
    {
        /**@var AuthorizationChecker $authorizationChecker */
        $authorizationChecker = $this->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_USER')) {
            return new PrettyJsonResponse([
                'response' => true,
                'status' => 'authorized',
                'message' => 'Ready to response',
            ], 200);
        }

        return new PrettyJsonResponse([
            'response' => true,
            'status' => 'unauthorized',
            'message' => 'To work with API authorize!',
        ], 403);
    }

    /**
     * @Route("/api/authenticate", name="api_main")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return PrettyJsonResponse|NotFoundHttpException
     */
    public function authenticateAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);

        $form = $this->createForm(AuthenticateType::class);
        $form->submit($requestData);

        if ($form->isValid()) {
            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'username' => $form->get('username')->getData(),
                    'password' => $form->get('password')->getData()
                ]);

            if (null !== $user) {
                return new PrettyJsonResponse([
                    'token' => $user->getApiToken()
                ]);
            }
        }

       return new PrettyJsonResponse([], 404);
    }

//    /**
//     * @param $request
//     * @Route("/{url}", name="remove_trailing_slash",
//     *     requirements={"url" = ".*\/$"}, methods={"GET"})
//     * @return RedirectResponse
//     */
//    public function removeTrailingSlashAction(Request $request)
//    {
//        $pathInfo = $request->getPathInfo();
//        $requestUri = $request->getRequestUri();
//        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);
//
//        return $this->redirect($url, 301);
//    }

    /**
     * @param string $route
     * @param array $parameters
     * @param int $referenceType
     * @return mixed
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('app.amazing_route_generator')->generate($route, $parameters, $referenceType);
    }
}