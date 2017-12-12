<?php

namespace AppBundle\Controller\Api\Task;

use AppBundle\Component\PrettyJsonResponse;
use AppBundle\Controller\Api\ApiController;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class TaskController
 * @package AppBundle\Controller\API\Task
 */
class TaskController extends ApiController
{
    /**
     * @Route("api/tasks", name="api_tasks_all")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAllTasks(Request $request)
    {
        /* @var $user User */
        $user = $this->getUser();

        $taskResponseGenerator = $this->get('app.task_response_arr_generator');

        $tasks = [
            'response' => true,
            'links' => [
                'self' => $this->generateUrl('api_tasks_all', [], 0)
            ],
            'data' => []
        ];

        foreach ($user->getTaskGroups() as $taskGroup) {
            foreach ($taskGroup->getTasks() as $task) {
                /**@var Task $task */
                $tasks['data'][] = $taskResponseGenerator->generateTaskResponse($task);
            }
        }

        return new PrettyJsonResponse($tasks, 200);
    }


    /**
     * @Route("api/tasks", name="api_task_create")
     * @Method({"POST"})
     *
     * @param $request
     *
     * @return Response
     */
    public function createTask(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        if ($request->get('group')) {
            $taskGroup = $em->getRepository(TaskGroup::class)->getByUserAndId($user, $request->get('group'));
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'Missing "group"'
            ], 400);
        }

        if ($taskGroup) {
            $task = new Task();

            try {
                $task->setDescription($request->get('description'))
                    ->setType($request->get('type') ? $request->get('type') : $task->getType())
                    ->setStatus($request->get('status') ? $request->get('status') : $task->getStatus())
                    ->setEndDate(new \DateTime())
                    ->setStateFlag(true);

                $taskGroup->addTask($task);
                $em->persist($taskGroup);
                $em->persist($task);
                $em->flush();
            } catch (\Exception $exception) {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => $exception->getMessage()
                ], 400);
            }

            $taskResponseGenerator = $this->get('app.task_response_arr_generator');

            return new PrettyJsonResponse([
                    'response' => true,
                    'status' => 'created'
                ] + $taskResponseGenerator->generateTaskResponse($task),
                201);
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'TaskGroup not found!'
            ], 404);
        }
    }

    /**
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task_edit")
     * @Method({"PUT"})
     *
     * @param $request
     * @param Task $task
     *
     * @return Response
     */
    public function editTask(Request $request, Task $task = null)
    {
        if ($task) {

            $apiToken = $request->get('api_token');

            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'apiToken' => $apiToken,
                ]);

            if (!$user) {
                return $this->notValidApiToken();
            }

            $em = $this->getDoctrine()->getManager();

            $taskCreator = $em->getRepository(User::class)->getTaskCreatorUser($task);
            if ($taskCreator->getId() === $user->getId()) {
                /**@var TaskGroup $taskGroup */
                $taskGroup = $em
                    ->getRepository(TaskGroup::class)
                    ->getByUserAndId($user, $request->get('group') ? $request->get('group') : $task->getGroup()->getId());
                if ($request->get('group') && !$taskGroup) {
                    return new PrettyJsonResponse([
                        'response' => true,
                        'error' => 'TaskGroup not found!'
                    ], 404);
                }

                try {
                    // TODO: Use validator->validate and Assert in Entity
                    $task->setDescription($request->get('description') ? $request->get('description') : $task->getDescription())
                        ->setType($request->get('type') ? $request->get('type') : $task->getType())
                        ->setStatus($request->get('status') ? $request->get('status') : $task->getStatus())
                        ->setEndDate($request->get('date') ? $request->get('date') : $task->getEndDate())
                        ->setStateFlag(true);

                    $taskGroup->addTask($task);
                    $em->persist($taskGroup);
                    $em->persist($task);
                    $em->flush();
                } catch (\Exception $exception) {
                    return new PrettyJsonResponse([
                        'response' => true,
                        'error' => $exception->getMessage()
                    ], 400);
                }

                $taskResponseGenerator = $this->get('app.task_response_arr_generator');
                return new PrettyJsonResponse([
                        'response' => true,
                        'status' => 'edited'
                    ] + $taskResponseGenerator->generateTaskResponse($task),
                    200);
            }

            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'Not Allowed for this user!'
            ], 401);
        } else {

            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'Task not found!'
            ], 404);
        }
    }

    /**
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task_remove")
     * @Method({"DELETE"})
     *
     * @param Task $task
     *
     * @return Response
     */
    public function removeTask(Request $request, Task $task = null)
    {
        if ($task) {

            $apiToken = $request->get('api_token');

            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'apiToken' => $apiToken,
                ]);

            if (!$user) {
                return $this->notValidApiToken();
            }

            $em = $this->getDoctrine()->getManager();

            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $taskCreator = $userRepo->getTaskCreatorUser($task);

            if ($taskCreator->getId() === $user->getId()) {
                $em->remove($task);
                $em->flush();
                return new PrettyJsonResponse([
                    'response' => true,
                    'status' => 'deleted'
                ], 410);
            } else {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'Task not found!'
            ], 404);
        }
    }

    /**
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param Task $task
     *
     * @return Response
     */
    public function getTask(Request $request, Task $task = null)
    {
        if ($task) {

            $apiToken = $request->get('api_token');

            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'apiToken' => $apiToken,
                ]);

            if (!$user) {
                return $this->notValidApiToken();
            }

            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            /**@var UserRepository $userRepo */
            $taskCreator = $userRepo->getTaskCreatorUser($task);
            if ($taskCreator->getId() === $user->getId()) {
                $taskResponseGenerator = $this->get('app.task_response_arr_generator');
                return new PrettyJsonResponse(
                    ['response' => true] + $taskResponseGenerator->generateTaskResponse($task),
                    200);
            } else {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'Task not found!'
            ], 404);
        }
    }
}
