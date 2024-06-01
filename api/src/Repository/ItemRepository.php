<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
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
