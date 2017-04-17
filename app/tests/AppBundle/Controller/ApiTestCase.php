<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\BrowserKit\Client;

/**
 * Class ApiTestCase
 * @package Tests\AppBundle\Controller
 */
class ApiTestCase extends WebUserTestCase
{
    private $client;

    /**
     * ApiTestCase constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->client = $this::createClient(['environment' => 'test']);
        $user = $this->client->getContainer()->get('doctrine')
            ->getRepository("AppBundle:User")
            ->loadUserByUsername('girz298');
        $this->logIn($this->client, $user);
        parent::__construct($name, $data, $dataName);
    }

    public function testGetAllTasksApi()
    {
        $crawler = $this->client->request('GET', '/api/tasks');
        $arrOfTasks = current(json_decode($this->client->getResponse()->getContent(), true));
        echo "Checking status code:\n";
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
        echo "Checking result array:\n";
        $this->assertArrayHasKey(1, $arrOfTasks);
    }

    public function testGetAllTaskGroupsApi()
    {
        $crawler = $this->client->request('GET', '/api/task-groups');
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey(1, $arrOfTaskGroups);
    }

    public function testTaskCreateAndDeleteApi()
    {
        $crawler = $this->client->request('POST', '/api/tasks', [
            'description' => 'FromTests22222',
            'type' => 1,
            'group' => 4,
        ]);

        echo "Checking status code when create Task:\n";
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');

        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true);

        $crawler = $this->client->request('DELETE', $arrOfTaskGroups['links']['self']);

        $this->assertEquals(410, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
    }
}
