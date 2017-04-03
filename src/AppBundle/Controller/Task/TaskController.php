<?php

namespace AppBundle\Controller\Task;


use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Form\Task\BaseTaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TaskController extends Controller
{
    /**
     * @Route("/task/create", name="task_create")
     */
    public function createTodoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BaseTaskType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $task = new Task();
            $task
                ->setDescription($form->get('description')->getData())
                ->setEndDate($form->get('end_date')->getData());
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_create');
        }

        return $this->render('task_crud/task_create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/task/group", name="task_group")
     */
    public function taskGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskGroup = new TaskGroup();
        $taskGroup->setDescription('Work');

        $task = new Task();
        $task
            ->setDescription('some description')
            ->setEndDate(new \DateTime())
            ->setStatus(Task::IMPORTANT_URGENT);

        $task2 = new Task();
        $task2
            ->setDescription('some description')
            ->setEndDate(new \DateTime());

        $taskGroup->addTask($task);
        $taskGroup->addTask($task2);

        $em->persist($taskGroup);
        $em->persist($task);
        $em->persist($task2);
        $em->flush();

        return new Response('success!');
    }
}