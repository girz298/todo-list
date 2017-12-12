<?php

namespace AppBundle\Security;

use AppBundle\DataFixtures\ORM\UserFixture;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getUsernameForApiKey($apiKey)
    {
        $username = null;

        if ($apiKey === UserFixture::API_TOKEN) {
            $username = UserFixture::JACK_KOVZEL_USER_NAME;
        }

        return $username;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->doctrine
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        $user->setRole('ROLE_API');

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
