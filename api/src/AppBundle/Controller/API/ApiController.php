<?php

namespace AppBundle\Controller\API;

use AppBundle\Component\PrettyJsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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