<?php

namespace AppBundle\Service;


use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class TaskResponseArrGenerator
{
    /**@var Router $router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function generateTaskResponse(Task $task)
    {
        return [
            'links' => [
                'self' => $this->router->generate(
                    'api_task',
                    ['task' => $task->getId()],
                    0
                ),
                'group' => $this->router->generate(
                    'api_task_group',
                    ['taskGroup' => $task->getGroup()->getId()],
                    0
                ),
            ],
            'data' => ['id' => $task->getId(),
                'description' => $task->getDescription(),
                'state_flag' => $task->getStateFlag(),
                'status' => $task->getStatus(),
                'type' => $task->getType(),
                'group' => $task->getGroup()->getId(),
            ]
        ];
    }
}