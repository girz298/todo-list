<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 19.04.17
 * Time: 0:02
 */

namespace AppBundle\API\TaskGroup;


use Tests\AppBundle\Base\WebUserTestCase;

class ApiTaskGroupEditingTest extends WebUserTestCase
{
    public function testEditAllTaskGroups()
    {
        $this->client->request('GET', '/api/task-groups');
        $arrOfTaskGroups = json_decode($this->client->getResponse()->getContent(), true)['data'];
        foreach ($arrOfTaskGroups as $taskGroup) {
            $this->client->request('PUT', $taskGroup['links']['self'], [
                'description' => 'Edited TaskGroup'
            ]);
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Status code not correct!');
        }
    }
}