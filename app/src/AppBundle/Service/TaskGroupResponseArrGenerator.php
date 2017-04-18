<?php

namespace AppBundle\Service;

use AppBundle\Entity\TaskGroup;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class TaskGroupResponseArrGenerator
{
    /**@var Router $router */
    private $router;
    private $taskResponseArrGenerator;

    public function __construct(Router $router, TaskResponseArrGenerator $taskResponseArrGenerator)
    {
        $this->router = $router;
        $this->taskResponseArrGenerator = $taskResponseArrGenerator;
    }

    public function generateTaskGroupResponse(TaskGroup $taskGroup, $includeTasksFlag = false)
    {
        $response = [
            'links' => [
                'self' => $this->router->generate(
                    'api_task_group',
                    ['taskGroup' => $taskGroup->getId()],
                    0
                )
            ],
            'data' => [
                'id' => $taskGroup->getId(),
                'description' => $taskGroup->getDescription()
            ],
        ];

        if ($includeTasksFlag) {
            $tasksResponse = [];
            foreach ($taskGroup->getTasks() as $task) {
                $tasksResponse[] = $this->taskResponseArrGenerator->generateTaskResponse($task);
            }
            $response['data']['tasks'] = $tasksResponse;
        }

        return $response;
    }
}