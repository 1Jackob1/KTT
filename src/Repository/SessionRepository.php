<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class SessionRepository extends BaseRepository
{
    public const ROOT_ALIAS = 'session';

    /**
     * @param User $user
     * @param Task $task
     *
     * @throws NonUniqueResultException
     *
     * @return Session
     */
    public function getOpenedAndValidSession(User $user, Task $task)
    {
        $qb = $this
            ->createQueryBuilder(self::ROOT_ALIAS)
            ->addSelect('user', 'task')
            ->leftJoin('session.user', 'user')
            ->leftJoin('session.task', 'task')
            ->andWhere('session.user = :user')
            ->andWhere('session.task = :task')
            ->andWhere('session.valid = true')
            ->andWhere('session.startDate IS NOT NULL AND session.endDate IS NULL')
            ->setParameter('user', $user)
            ->setParameter('task', $task)
        ;

        $session = $qb->getQuery()->getOneOrNullResult();

        return $session;
    }
}