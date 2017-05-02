<?php

namespace AppBundle\Controller\API\TaskGroup;


use AppBundle\Controller\API\ApiController;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskGroup;
use AppBundle\Entity\User;
use AppBundle\Component\PrettyJsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Class TaskGroupController
 * @package AppBundle\Controller\API\TaskGroup
 */
class TaskGroupController extends ApiController
{

    /**
     * @param Request $request
     * @Route("api/task-groups", name="api_task_groups_all")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getAllTaskGroups(Request $request)
    {
        /**@var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $response = [
            'response' => true,
            'links' => [
                'self' => $this->generateUrl('api_task_groups_all', [], 0)
            ],
            'data' => []
        ];
        $includeTasksFlag = $request->get('includeTasks') ? true : false;
        /**@var TaskGroup $taskGroup */
        $taskGroupResponseGenerator = $this->get('app.task_group_response_arr_generator');
        foreach ($user->getTaskGroups() as $taskGroup) {
            $response['data'][] = $taskGroupResponseGenerator->generateTaskGroupResponse($taskGroup, $includeTasksFlag);
        }
        return new PrettyJsonResponse($response, 200);
    }

    /**
     * @param TaskGroup $taskGroup
     * @Route("api/task-groups/{taskGroup}", requirements={"taskGroup" = "[0-9]+"}, name="api_task_group")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getTaskGroup(TaskGroup $taskGroup = null)
    {
        /**@var TaskGroup $taskGroup */
        if ($taskGroup) {
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($taskGroup->getUser()->getId() === $user->getId()) {
                $taskGroupResponseGenerator = $this->get('app.task_group_response_arr_generator');
                $response = ['response' => true];
                return new PrettyJsonResponse($response + $taskGroupResponseGenerator->generateTaskGroupResponse($taskGroup, true));
            } else {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'TaskGroup not found.'
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @Route("api/task-groups", name="api_task_groups_create")
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function createTaskGroup(Request $request)
    {
        // TODO: Use validator->validate and Assert in Entity
        if ($request->get('description')) {
            try {
                /**@var User $user */
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                $em = $this->getDoctrine()->getManager();
                $taskGroup = new TaskGroup();
                $taskGroup
                    ->setUser($user)
                    ->setDescription($request->get('description'));
                $em->persist($taskGroup);
                $em->flush();
            } catch (\Exception $exception) {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => $exception->getMessage()
                ], 400);
            }
            return new PrettyJsonResponse([
                'response' => true,
                'status' => 'created'
            ],
                201);
        } else {
            return new PrettyJsonResponse([
                'success' => true,
                'error' => 'Description not valid!'
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @param TaskGroup $taskGroup
     * @Route("api/task-groups/{taskGroup}", requirements={"taskGroup" = "[0-9]+"}, name="api_task_groups_edit")
     * @Method({"PUT"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function editTaskGroup(Request $request, TaskGroup $taskGroup = null)
    {
        if ($taskGroup) {
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($taskGroup->getUser()->getId() === $user->getId()) {
                if ($request->get('description')) {
                    try {
                        $em = $this->getDoctrine()->getManager();
                        $taskGroup
                            ->setDescription($request->get('description') ? $request->get('description') :
                                $taskGroup->getDescription());
                        $em->persist($taskGroup);
                        $em->flush();
                    } catch (\Exception $exception) {
                        return new PrettyJsonResponse([
                            'response' => true,
                            'error' => $exception->getMessage()
                        ], 400);
                    }
                    return new PrettyJsonResponse([
                        'response' => true,
                        'status' => 'edited'
                    ],
                        200);
                } else {
                    return new PrettyJsonResponse([
                        'response' => true,
                        'error' => 'Description not valid!'
                    ], 400);
                }
            } else {
                return new PrettyJsonResponse([
                    'response' => true,
                    'error' => 'Not Allowed for this user!'
                ], 401);
            }
        } else {
            return new PrettyJsonResponse([
                'response' => true,
                'error' => 'TaskGroup not found!'
            ], 404);
        }
    }

    /**
     * @param TaskGroup $taskGroup
     * @Route("api/task-groups/{taskGroup}", requirements={"taskGroup" = "[0-9]+"}, name="api_task_group_remove")
     * @Method({"DELETE"})
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function removeTaskGroup(TaskGroup $taskGroup = null)
    {
        if ($taskGroup) {
            $em = $this->getDoctrine()->getManager();
            /**@var User $user */
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            if ($taskGroup->getUser()->getId() === $user->getId()) {
                $em->remove($taskGroup);
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
                'error' => 'TaskGroup not found!'
            ], 404);
        }
    }
}