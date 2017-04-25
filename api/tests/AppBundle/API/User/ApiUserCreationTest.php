<?php

namespace AppBundle\API\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiUserCreationTest
 * @package AppBundle\API\User
 */
class ApiUserCreationTest extends WebTestCase
{
    public function testUserCreation(){
        $client = $this::createClient(['environment' => 'test']);
        $username = $client->getContainer()->getParameter('test_username');
        $client->request('GET', '/api/logout');
        $user = $client->getContainer()->get('doctrine')
            ->getRepository("AppBundle:User")
            ->loadUserByUsername($client->getContainer()->getParameter('test_username'));
        if ($user){
            echo 'This User already exist!';
        } else {
            $client->request('POST', '/api/register', [
                'username' => $username,
                'password' => '123456',
                'email' => $username . '@gmail.com'
            ]);

            $this->assertEquals(201, $client->getResponse()->getStatusCode(), 'Status code not correct!');
        }
    }

    public function testNotValidCreation()
    {
        $client = $this::createClient(['environment' => 'test']);
        $username = $client->getContainer()->getParameter('test_username');

        $client->request('GET', '/api/logout');

        $client->request('POST', '/api/register', [
            'username' => $username
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Status code not correct!');
    }

    public function testConflictCreation() {
        $client = $this::createClient(['environment' => 'test']);
        $username = $client->getContainer()->getParameter('test_username');

        $client->request('GET', '/api/logout');

        $client->request('POST', '/api/register', [
            'username' => $username,
            'password' => '123456',
            'email' => $username . '@gmail.com'
        ]);

        $this->assertEquals(409, $client->getResponse()->getStatusCode(), 'Status code not correct!');
    }
}