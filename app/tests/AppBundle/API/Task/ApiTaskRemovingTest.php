<?php

namespace AppBundle\API\Task;


use Tests\AppBundle\Base\WebUserTestCase;

class ApiTaskRemovingTest extends WebUserTestCase
{
    public function testTaskRemoving()
    {
        $this->client->request('GET', '/api/task-groups', ['includeTasks' => 1]);
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true)['data'];
        foreach ($arrOfTaskGroups as $taskGroup) {
            foreach ($taskGroup['data']['tasks'] as $task) {
                $this->client->request('DELETE', $task['links']['self']);
                $this->assertEquals(410, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
            }
        }
    }
}