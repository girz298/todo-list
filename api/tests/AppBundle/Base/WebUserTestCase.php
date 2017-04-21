<?php

namespace Tests\AppBundle\Base;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebUserTestCase extends WebTestCase
{
    protected $client;

    /**
     * ApiTaskGroupCreationTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->client = $this::createClient(['environment' => 'test']);
        $user = $this->client->getContainer()->get('doctrine')
            ->getRepository("AppBundle:User")
            ->loadUserByUsername($this->client->getContainer()->getParameter('test_username'));
        $this->logIn($this->client, $user);
        parent::__construct($name, $data, $dataName);
    }

    protected function logIn(Client $client, User $user)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken($user->getUsername(), null, $firewall, $user->getRoles());
        $token->setUser($user);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}