<?php

namespace AppBundle\Controller\Task;


use AppBundle\Entity\Task;
use AppBundle\Form\Task\BaseTaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
}