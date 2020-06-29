<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserRepository extends EntityRepository
{
    /**
     * @param array $data
     *
     * @param PaginatorInterface $paginator
     *
     * @return PaginationInterface
     */
    public function getPaginatedUsers(array $data, PaginatorInterface $paginator)
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? 30;

        unset($data['page'], $data['per_page']);

        $qb = $this->createQueryBuilder('user');

        foreach ($data as $fieldName => $fieldValue) {
            $qb
                ->andWhere("user.{$fieldName} IN (:{$fieldName}_value)")
                ->setParameter("{$fieldName}_value", $fieldValue)
            ;
        }

        return $paginator->paginate($qb, $page, $perPage);
    }
}