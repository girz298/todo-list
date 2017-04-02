<?php

namespace AppBundle\Controller\Todo;


use AppBundle\Entity\Todo;
use AppBundle\Form\Todo\BaseTodoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class TodoController extends Controller
{
    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createTodoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(BaseTodoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $todo = new Todo();
            $todo
                ->setDescription($form->get('description')->getData())
                ->setEndDate($form->get('end_date')->getData());
            $em->persist($todo);
            $em->flush();

            return $this->redirectToRoute('todo_create');
        }

        return $this->render('todo_crud/todo_create.html.twig', ['form' => $form->createView()]);
    }
}