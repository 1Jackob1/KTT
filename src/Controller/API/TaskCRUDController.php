<?php

namespace App\Controller\API;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Util\MessageUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @Rest\Route(path="api/tasks")
 */
class TaskCRUDController extends BaseCRUDController
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
        return $this->getItemsAction($request, Task::class, $paginator, [User::FULL_CARD, Task::FULL_CARD]);
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
        return $this->createItemAction($request, TaskFormType::class, Task::class, [User::FULL_CARD, Task::FULL_CARD]);
    }

    /**
     * @Rest\Patch(name="update_task")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateItemAction($request, TaskFormType::class, Task::class);
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
        return $this->deleteItemAction($request, Task::class);
    }
}