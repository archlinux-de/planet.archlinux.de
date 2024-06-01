<?php

namespace App\Repository;

use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Feed>
 */
class FeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    /**
     * @return Paginator<feed>
     */
    public function findLatest(int $offset, int $limit): Paginator
    {
        $queryBuilder = $this
            ->createQueryBuilder('feed')
            ->orderBy('feed.lastModified', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder);
    }

    /**
     * @param string[] $urls
     * @return Feed[]
     */
    public function findAllExceptByUrls(array $urls): array
    {
        if (empty($urls)) {
            /** @var Feed[] $feeds */
            $feeds = $this->findAll();
            return $feeds;
        }

        return $this
            ->createQueryBuilder('feed')
            ->where('feed.url NOT IN (:urls)')
            ->setParameter('urls', $urls)
            ->getQuery()
            ->getResult();
    }
}
