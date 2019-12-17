<?php

namespace App\Repository;

use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FeedRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
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

    /**
     * @param string[] $urls
     * @return Feed[]
     */
    public function findAllExceptByUrls(array $urls): array
    {
        if (empty($urls)) {
            return $this->findAll();
        } else {
            return $this
                ->createQueryBuilder('feed')
                ->where('feed.url NOT IN (:urls)')
                ->setParameter('urls', $urls)
                ->getQuery()
                ->getResult();
        }
    }
}
