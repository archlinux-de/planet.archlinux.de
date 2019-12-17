<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param int $limit
     * @return Item[]
     */
    public function findLatest(int $limit): array
    {
        return $this
            ->createQueryBuilder('item')
            ->orderBy('item.lastModified', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
