<?php

namespace AppBundle\Controller\Task;


use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use AppBundle\Form\Task\BaseTaskType;
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
        /**@var User $user*/
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
     * @Route("/task-group/all", name="all_task_groups")
     * @Method({"POST","GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getAllTaskGroups()
    {
        /**@var User $user*/
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        /**@var TaskGroup $taskGroup*/
//        $taskGroup = $user->getTastGroups()->first();
        $response = [];
        foreach ($user->getTaskGroups() as $taskGroup) {
            $response[] = [
                'id' => $taskGroup->getId(),
                'description' => $taskGroup->getDescription()
            ];
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/task/all", name="all_task")
     * @Method({"POST","GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getAllTask()
    {
        /**@var User $user*/
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        /**@var TaskGroup $taskGroup*/
//        $taskGroup = $user->getTastGroups()->first();
        $response = [];
        foreach ($user->getTaskGroups() as $taskGroup) {
            $tasks = [];
            foreach ($taskGroup->getTasks() as $task) {
                /**@var Task $task*/
                $tasks[] = [
                    'id' => $task->getId(),
                    'description' => $task->getDescription(),
                    'state_flag' => $task->getStateFlag(),
                    'status' => $task->getStatus(),
                    'type' => $task->getType(),
                ];
            }
            $response[] = [
                'id' => $taskGroup->getId(),
                'description' => $taskGroup->getDescription(),
                'tasks' => $tasks
            ];
        }
        return new JsonResponse($response);
    }
}