<?php

namespace AppBundle\Controller\API\Task;


use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use AppBundle\Form\Task\BaseTaskType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class TaskController extends Controller
{
    /**
     * @param Request $request
     * @Route("/task/create", name="task_create")
     * @Method({"POST","GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function createTodoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BaseTaskType::class);
        $form->handleRequest($request);
        /**@var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        if ($form->isSubmitted()) {
            $taskGroup = new TaskGroup();
            $taskGroup
                ->setUser($user)
                ->setDescription('Daily');
            $task = new Task();
            $task
                ->setGroup($taskGroup)
                ->setDescription($form->get('description')->getData())
                ->setEndDate($form->get('end_date')->getData())
                ->setType(Task::TYPE_DAILY_GOAL);
            $taskGroup->addTask($task);
            $em->persist($taskGroup);
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_create');
        }

        return $this->render('task_crud/task_create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("api/tasks", name="api_tasks_all")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getAllTasks()
    {
        /**@var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        /**@var TaskGroup $taskGroup */
        $response = [];
        foreach ($user->getTaskGroups() as $taskGroup) {
            $tasks = [];
            foreach ($taskGroup->getTasks() as $task) {
                /**@var Task $task */
                $tasks[] = [
                    'id' => $task->getId(),
                    'description' => $task->getDescription(),
                    'state_flag' => $task->getStateFlag(),
                    'status' => $task->getStatus(),
                    'type' => $task->getType()
                ];
            }
            $response[] = [
                'response' => true,
                'id' => $taskGroup->getId(),
                'description' => $taskGroup->getDescription(),
                'tasks' => $tasks
            ];
        }
        return new JsonResponse($response, 200);
    }


    /**
     * @param $request
     * @Route("api/tasks", name="api_task_create")
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function createTask(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $jsonRequest = new ArrayCollection(json_decode($request->getContent(), true));
        /**@var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $taskGroup = $em->getRepository(TaskGroup::class)->getByUserAndId($user, $jsonRequest->get('group'));
        if ($taskGroup) {
            $task = new Task();

            try {
                $task->setDescription($jsonRequest->get('description'))
                    ->setType($jsonRequest->get('type'))
                    ->setStatus($jsonRequest->get('status'))
                    ->setEndDate(new \DateTime())
                    ->setStateFlag(true);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    'success' => true,
                    'error' => $exception->getMessage()
                ], 400);
            }

            $taskGroup->addTask($task);
            $em->persist($taskGroup);
            $em->persist($task);
            $em->flush();

            return new JsonResponse([
                'response' => true,
                'status' => 'created'
            ], 201);
        } else {
            return new JsonResponse([
                'response' => true,
                'error' => 'TaskGroup not exist!'
            ], 400);
        }
    }

    /**
     * @param $request
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task_edit")
     * @Method({"PUT"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function editTask(Request $request, Task $task = null)
    {

        if ($task) {
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $taskCreator = $em->getRepository(User::class)->getTaskCreatorUser($task);
            if ($taskCreator->getId() === $user->getId()) {
                $jsonRequest = new ArrayCollection(json_decode($request->getContent(), true));
                /**@var TaskGroup $taskGroup */
                $taskGroup = $em
                    ->getRepository(TaskGroup::class)
                    ->getByUserAndId($user, $jsonRequest->get('group')?$jsonRequest->get('group'):$task->getGroup()->getId());
                if ($jsonRequest->get('group') && !$taskGroup) {
                    return new JsonResponse([
                        'response' => true,
                        'error' => 'TaskGroup not exist!'
                    ], 400);
                }

                try {
                    $task->setDescription($jsonRequest->get('description')?$jsonRequest->get('description'):$task->getDescription())
                        ->setType($jsonRequest->get('type')?$jsonRequest->get('type'):$task->getType())
                        ->setStatus($jsonRequest->get('status')?$jsonRequest->get('status'):$task->getStatus())
                        ->setEndDate($jsonRequest->get('date')?$jsonRequest->get('date'):$task->getEndDate())
                        ->setStateFlag(true);

                    $taskGroup->addTask($task);
                    $em->persist($taskGroup);
                    $em->persist($task);
                    $em->flush();
                } catch (\Exception $exception) {
                    return new JsonResponse([
                        'response' => true,
                        'error' => $exception->getMessage()
                    ], 400);
                }

                return new JsonResponse([
                    'response' => true,
                    'status' => 'edited'
                ], 200);
            }
            return new JsonResponse([
                'response' => true,
                'error' => 'Not Allowed for this user!'
            ], 401);
        } else {
            return new JsonResponse([
                'response' => true,
                'error' => 'Task not exist!'
            ], 400);
        }
    }

    /**
     * @param Task $task
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task_remove")
     * @Method({"DELETE"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function removeTask(Task $task = null)
    {
        if ($task) {
            $em = $this->getDoctrine()->getManager();
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $taskCreator = $userRepo->getTaskCreatorUser($task);

            if ($taskCreator->getId() === $user->getId()) {
                $em->remove($task);
                $em->flush();
                return new JsonResponse([
                    'response' => true,
                    'status' => 'deleted'
                ], 200);
            } else {
                return new JsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new JsonResponse([
                'response' => true,
                'error' => 'Task not exist!'
            ], 401);
        }
    }

    /**
     * @param Task $task
     * @Route("api/tasks/{task}", requirements={"task" = "[0-9]+"}, name="api_task")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getTask(Task $task = null)
    {
        if ($task) {
            $em = $this->getDoctrine()->getManager();
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $taskCreator = $userRepo->getTaskCreatorUser($task);

            if ($taskCreator->getId() === $user->getId()) {
                return new JsonResponse([
                    'task_group_link' => $this->generateUrl(
                        'api_task_group',
                        ['taskGroup' => $task->getGroup()->getId()],
                        0
                    ),
                    'data' => ['id' => $task->getId(),
                        'description' => $task->getDescription(),
                        'state_flag' => $task->getStateFlag(),
                        'status' => $task->getStatus(),
                        'type' => $task->getType(),
                        'group' => $task->getGroup()->getId(),
                    ],
                ], 200);
            } else {
                return new JsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new JsonResponse([
                'response' => true,
                'error' => 'Task not exist!'
            ], 401);
        }
    }
}