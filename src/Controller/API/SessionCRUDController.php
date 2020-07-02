<?php

namespace App\Controller\API;

use App\Entity\Session;
use App\Entity\Task;
use App\Entity\User;
use App\Form\SessionModelType;
use App\Model\SessionModel;
use App\Repository\SessionRepository;
use App\Util\MessageUtil;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route(path="api/sessions")
 */
class SessionCRUDController extends AbstractCRUDController
{
    /**
     * @Rest\Get(name="get_sessions")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function getSessionsAction(Request $request, PaginatorInterface $paginator)
    {
        return $this->getItemsAction($request, $paginator, [Session::FULL_CARD, User::FULL_CARD, Task::FULL_CARD]);
    }

    /**
     * @Rest\Post(path="/start", name="start_session")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function startSessionAction(Request $request)
    {
        $form = $this->createForm(SessionModelType::class);

        /** @var SessionModel $sessionData */
        $sessionData = $this->handleRequestWithJSONContent($request, $form);
        $previouslyOpenedSession = $this->getRepository()->getOpenedAndValidSession($sessionData->getUser(), $sessionData->getTask());

        if ($previouslyOpenedSession) {
            $previouslyOpenedSession->setValid(false);
        }

        $session = (new Session())
            ->setUser($sessionData->getUser())
            ->setTask($sessionData->getTask())
            ->setStartDate((new \DateTime())->setTimestamp($sessionData->getTimestamp()))
            ->setValid(true)
        ;

        $this->save($session);

        return $this->getResponse($session);
    }

    /**
     * @Rest\Post(path="/stop", name="stop_session")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function stopSessionAction(Request $request)
    {
        $form = $this->createForm(SessionModelType::class);

        /** @var SessionModel $sessionData */
        $sessionData = $this->handleRequestWithJSONContent($request, $form);
        $previouslyOpenedSession = $this->getRepository()->getOpenedAndValidSession($sessionData->getUser(), $sessionData->getTask());

        if (!$previouslyOpenedSession) {
            return $this->getResponse('', MessageUtil::ERROR, Response::HTTP_BAD_REQUEST);
        }

        $previouslyOpenedSession->setEndDate((new \DateTime())->setTimestamp($sessionData->getTimestamp()));

        $this->getDoctrine()->getManager()->flush();

        return $this->getResponse($previouslyOpenedSession);
    }

    /**
     * @Rest\Delete(name="delete_session")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteSessionAction(Request $request)
    {
        return $this->deleteItemAction($request);
    }

    /**
     * @return SessionRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Session::class);
    }
}