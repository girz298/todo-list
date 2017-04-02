<?php

namespace AppBundle\Controller\Todo;

use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TodoController extends Controller
{
    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createTodoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $todo = new Todo();
        $todo
            ->setDescription('Sbegat\' Pohavat\'')
            ->setEndDate(new \DateTime('2005-08-15T15:52:01+00:00'));
        $em->persist($todo);
        $em->flush();

        return new Response($todo->getDescription() . '<br>' . $todo->getEndDate()->format(DATE_W3C));
    }
}