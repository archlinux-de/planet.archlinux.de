<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ItemRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
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

    /**
     * @param \App\Entity\Feed $feed
     * @param array $itemIds
     * @return Item[]
     */
    public function findAllExceptByIds(\App\Entity\Feed $feed, array $itemIds): array
    {
        if (empty($itemIds)) {
            return $this
                ->createQueryBuilder('item')
                ->where('item.feed = :feed')
                ->setParameter('feed', $feed)
                ->getQuery()
                ->getResult();
        } else {
            return $this
                ->createQueryBuilder('item')
                ->where('item.feed = :feed')
                ->andWhere('item.publicId NOT IN (:itemIds)')
                ->setParameter('feed', $feed)
                ->setParameter('itemIds', $itemIds)
                ->getQuery()
                ->getResult();
        }
    }
}
