<?php

namespace App\Repository;

use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FeedRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    /**
     * @return Feed[]
     */
    public function findLatest(): array
    {
        return $this
            ->createQueryBuilder('feed')
            ->orderBy('feed.lastModified', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
