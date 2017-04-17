<?php

namespace Tests\AppBundle\Controller;

class ApiTestCase extends WebUserTestCase
{
    public function testIndex()
    {

        $client = $this::createClient();
        $user = $client->getContainer()->get('doctrine')
            ->getRepository("AppBundle:User")
            ->loadUserByUsername('girz298');
        $this->logIn($client, $user);

        $crawler = $client->request('GET', '/api/task-groups/6');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Allowed', $crawler->text());
    }
}
