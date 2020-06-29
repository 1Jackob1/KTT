<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Form\UserFormType;
use App\Util\MessageUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @Rest\Route(path="api/users")
 */
class UserController extends BaseController
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
        $data = $this->decodeJsonContent($request);

        $paginatedResult = $this->getDoctrine()->getManager()->getRepository(User::class)->getPaginatedUsers($data, $paginator);

        return $this->getResponse($paginatedResult, MessageUtil::SUCCESS, Response::HTTP_OK, [User::FULL_CARD]);
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
        $form = $this->createForm(UserFormType::class);

        try {
            $user = $this->handleRequestWithJSONContent($request, $form);
        } catch (Throwable $e) {
            $this->logCriticalError('Error while handling form.', $e);

            return $this->getResponse(MessageUtil::VALIDATE_FORM, MessageUtil::ERROR, 400);
        }


        try {
            $this->save($user);
        } catch (Throwable $e) {
            $this->logCriticalError('Error while saving user.', $e);

            return $this->getResponse(MessageUtil::CAN_NOT_SAVE, MessageUtil::ERROR, 400);
        }

        return $this->getResponse($user, MessageUtil::SUCCESS, Response::HTTP_OK, [User::FULL_CARD]);
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
        $data = $this->decodeJsonContent($request);
        $user = $this->getDoctrine()->getRepository(User::class)->find($data['id'] ?? -1);

        if (!$user) {
            return $this->getResponse(null,MessageUtil::CAN_NOT_FIND_OBJECT, 400);
        }

        $form = $this->createForm(UserFormType::class, $user);

        $updatedUser = $this->handleRequestWithJSONContent($request, $form);

        $user->updateData($updatedUser);

        $this->getDoctrine()->getManager()->flush();

        return $this->getResponse($user);
    }

    /**
     * @Rest\Delete(name="delete_user")
     *
     * @param Request $request
     */
    public function deleteUserAction(Request $request)
    {
        $data = $this->decodeJsonContent($request);

        $user = $this->getDoctrine()->getRepository(User::class)->find($data['id'] ?? -1);

        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
    }
}