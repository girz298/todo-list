<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 01.05.17
 * Time: 20:53
 */

namespace AppBundle\Component\Authentication\Handler;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Routing\Router;

/**
 * Class LogoutSuccessHandlerInterface
 * @package AppBundle\Component\Authentication\Handler
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * LogoutSuccessHandlerInterface constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $redirectUrl = $this->router->generate('api_main');
        return new RedirectResponse($redirectUrl);
    }
}