<?php

namespace App\Controller\API;

use App\Entity\Task;
use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Util\MessageUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @Rest\Route(path="api/users")
 */
class UserCRUDController extends AbstractCRUDController
{
    /**
     * @Rest\Get(name="get_users")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getUsersAction(Request $request, PaginatorInterface $paginator)
    {
        return $this->getItemsAction($request, User::class, $paginator, [User::FULL_CARD, Task::FULL_CARD]);
    }

    /**
     * @Rest\Post(name="post_user")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createUserAction(Request $request)
    {
        return $this->createItemAction($request, UserFormType::class, User::class, [User::FULL_CARD, Task::FULL_CARD]);
    }

    /**
     * @Rest\Patch(name="update_user")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateItemAction($request, UserFormType::class, User::class);
    }

    /**
     * @Rest\Delete(name="delete_user")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteUserAction(Request $request)
    {
        return $this->deleteItemAction($request, User::class);
    }

    /**
     * @return UserRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }
}