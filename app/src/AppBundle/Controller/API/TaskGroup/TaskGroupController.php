<?php

namespace AppBundle\Controller\API\TaskGroup;

use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class TaskGroupController extends Controller
{

    /**
     * @Route("api/task-groups", name="api_task_groups_all")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return JsonResponse
     */
    public function getAllTaskGroups()
    {
        /**@var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $response = [];
        /**@var TaskGroup $taskGroup */
        foreach ($user->getTaskGroups() as $taskGroup) {
            $response[] = [
                'response' => true,
                'id' => $taskGroup->getId(),
                'description' => $taskGroup->getDescription(),
                'link' => $this->generateUrl('api_task_group', ['taskGroup' => $taskGroup->getId()], 0)
            ];
        }
        return new JsonResponse($response);
    }

    /**
     * @param TaskGroup $taskGroup
     * @Route("api/task-groups/{taskGroup}", requirements={"taskGroup" = "[0-9]+"}, name="api_task_group")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return JsonResponse
     */
    public function getTaskGroup(TaskGroup $taskGroup = null)
    {
        /**@var TaskGroup $taskGroup */
        if ($taskGroup) {
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($taskGroup->getUser()->getId() === $user->getId()) {
                $tasks = [];
                foreach ($taskGroup->getTasks() as $task) {
                    /**@var Task $task */
                    $tasks[] = [
                        'task_link' => $this->generateUrl(
                            'api_task',
                            ['task' => $task->getId()],
                            0
                        ),
                        'data' => ['id' => $task->getId(),
                            'description' => $task->getDescription(),
                            'state_flag' => $task->getStateFlag(),
                            'status' => $task->getStatus(),
                            'type' => $task->getType(),
                            'group' => $task->getGroup()->getId(),
                        ]
                    ];
                }

                $response = [
                    'task_group_link' => $this->generateUrl(
                        'api_task_group',
                        ['taskGroup' => $taskGroup->getId()],
                        0
                    ),
                    'data' => [
                        'id' => $taskGroup->getId(),
                        'description' => $taskGroup->getDescription(),
                        'tasks' => $tasks
                    ]
                ];
                return new JsonResponse($response);
            } else {
                return new JsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new JsonResponse([
                'response' => true,
                'error' => 'TaskGroup not found.'
            ], 401);
        }
    }
}