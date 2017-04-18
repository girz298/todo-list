<?php

namespace AppBundle\API\TaskGroup;


use Tests\AppBundle\Base\WebUserTestCase;

class ApiTaskGroupRemovingTest extends WebUserTestCase
{
    public function testRemovingAllTaskGroups()
    {
        $this->client->request('GET', 'api/task-groups');
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true)['data'];
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');

        foreach ($arrOfTaskGroups as $taskGroup) {
            $this->client->request('DELETE', $taskGroup['links']['self']);
            $this->assertEquals(410, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');

            $this->client->request('GET', $taskGroup['links']['self']);
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
        }
    }
}