<?php

namespace AppBundle\API\Task;


use Tests\AppBundle\Base\WebUserTestCase;

class ApiTaskEditingTest extends WebUserTestCase
{
    public function testEditTask()
    {
        $this->client->request('GET', '/api/task-groups', ['includeTasks' => 1]);
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true)['data'];
        if (empty($arrOfTaskGroups)) {
            throw new Exception('Array of task groups empty!');
        }

        foreach ($arrOfTaskGroups as $taskGroup) {
            foreach ($taskGroup['data']['tasks'] as $task) {
                $this->client->request('PUT', $task['links']['self'], [
                    'description' => 'Edited Task'
                ]);
                $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
            }
        }
    }
}