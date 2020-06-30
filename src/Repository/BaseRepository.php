<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class BaseRepository extends EntityRepository
{
    /**
     * @param array $data
     * @param PaginatorInterface $paginator
     * @param string|null $rootAlias
     *
     * @return PaginationInterface
     */
    public function getPaginatedData(array $data, PaginatorInterface $paginator, string $rootAlias = null)
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? 30;

        unset($data['page'], $data['per_page']);

        $rootAlias = $rootAlias ?? 'root_entity_alias';

        $qb = $this->createQueryBuilder($rootAlias);

        foreach ($data as $fieldName => $fieldValue) {
            $qb
                ->andWhere("{$rootAlias}.{$fieldName} IN (:{$fieldName}_value)")
                ->setParameter("{$fieldName}_value", $fieldValue)
            ;
        }

        return $paginator->paginate($qb, $page, $perPage);
    }
}