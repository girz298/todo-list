<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    const JACK_KOVZEL_EXAMPLE_COM = 'jack.kovzel@example.com';
    const API_TOKEN = 'BpwQwPNqVuv42PMCoijLYJd6QtPs8Cat';
    const JACK_KOVZEL_USER_NAME = 'jack.kovzel';
    const JACK_KOVZEL_PASSWORD = 'password';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setEmail(self::JACK_KOVZEL_EXAMPLE_COM)
            ->setUsername(self::JACK_KOVZEL_USER_NAME)
            ->setPassword(self::JACK_KOVZEL_PASSWORD)
            ->setApiToken(self::API_TOKEN);

        $manager->persist($user);

        $manager->flush();

        $this->addReference('user', $user);
    }
}
