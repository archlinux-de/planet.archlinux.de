<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ItemRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return Paginator<Item>
     */
    public function findLatest(int $offset, int $limit): Paginator
    {
        $queryBuilder = $this
            ->createQueryBuilder('item')
            ->orderBy('item.lastModified', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder);
    }
}
