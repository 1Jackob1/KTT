<?php

namespace App\Controller\API;

use App\Entity\Session;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Util\MessageUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @Rest\Route(path="api/tasks")
 */
class TaskCRUDController extends AbstractCRUDController
{

    /**
     * @Rest\Get(name="get_tasks")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function getTasksAction(Request $request, PaginatorInterface $paginator)
    {
        return $this->getItemsAction($request, $paginator, [User::FULL_CARD, Task::FULL_CARD, Session::FULL_CARD]);
    }

    /**
     * @Rest\Post(name="create_task")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createTaskAction(Request $request)
    {
        return $this->createItemAction($request, TaskFormType::class, Task::class, [User::FULL_CARD, Task::FULL_CARD, Session::FULL_CARD]);
    }

    /**
     * @Rest\Patch(name="update_task")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateTaskAction(Request $request)
    {
        return $this->updateItemAction($request, TaskFormType::class);
    }

    /**
     * @Rest\Delete(name="delete_task")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteTaskAction(Request $request)
    {
        return $this->deleteItemAction($request);
    }

    /**
     * @return TaskRepository
     */
    protected function getRepository()
    {
       return $this->getDoctrine()->getRepository(Task::class);
    }
}