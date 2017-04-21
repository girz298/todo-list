<?php

namespace AppBundle\API\Task;


use Symfony\Component\Config\Definition\Exception\Exception;
use Tests\AppBundle\Base\WebUserTestCase;

class ApiTaskCreationTest extends WebUserTestCase
{

    public function testValidCreateTask()
    {
        $this->client->request('GET', '/api/task-groups');
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true)['data'];
        if (empty($arrOfTaskGroups)) {
            throw new Exception('Array of task groups empty!');
        }

        foreach ($arrOfTaskGroups as $taskGroup) {
            $this->client->request('POST', '/api/tasks', [
                'description' => 'Test description for group №' . $taskGroup['data']['id'],
                'type' => 1,
                'status' => 3,
                'group' => $taskGroup['data']['id']
            ]);
            $this->assertEquals(201, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
        }
    }

    public function testNotValidCreateTask()
    {
        echo 'Trying to create task with not exist TaskGroup';
        $this->client->request('POST', '/api/tasks', [
            'description' => 'Test description for group №',
            'type' => 1,
            'status' => 3,
            'group' => -1
        ]);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
    }
}