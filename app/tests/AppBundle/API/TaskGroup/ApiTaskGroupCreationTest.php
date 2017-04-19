<?php

namespace AppBundle\API\TaskGroup;

use Tests\AppBundle\Base\WebUserTestCase;


class ApiTaskGroupCreationTest extends WebUserTestCase
{
    public function testValidCreateTaskGroup()
    {
        $this->client->request('POST', '/api/task-groups', [
            'description' => 'Test'
        ]);
        echo "Checking status code for valid request:\n";
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
    }

    public function testNotValidCreateTaskGroup()
    {

        $this->client->request('POST', '/api/task-groups', [
            'blabla' => 'Test'
        ]);
        echo "Checking status code for NOT valid request:\n";
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
    }
}