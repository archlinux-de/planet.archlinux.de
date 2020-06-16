<?php

namespace App\Tests\Repository;

use App\Entity\Feed;
use App\Repository\FeedRepository;
use SymfonyDatabaseTest\DatabaseTestCase;

class FeedRepositoryTest extends DatabaseTestCase
{
    public function testFindLatest(): void
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($feed);
        $entityManager->flush();
        $entityManager->clear();

        /** @var FeedRepository $feedRepository */
        $feedRepository = $this->getRepository(Feed::class);
        $feeds = iterator_to_array($feedRepository->findLatest(0, 10));
        $this->assertCount(1, $feeds);
        $this->assertEquals('https://www.archlinux.de/', $feeds[0]->getUrl());
    }

    public function testFindAllExceptByUrlsIfNoUrlsGiven(): void
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($feed);
        $entityManager->flush();
        $entityManager->clear();

        /** @var FeedRepository $feedRepository */
        $feedRepository = $this->getRepository(Feed::class);
        $this->assertCount(1, $feedRepository->findAllExceptByUrls([]));
        $this->assertEquals(
            'https://www.archlinux.de/',
            iterator_to_array($feedRepository->findLatest(0, 10))[0]->getUrl()
        );
    }

    public function testFindAllExceptByUrls(): void
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($feed);
        $entityManager->flush();
        $entityManager->clear();

        /** @var FeedRepository $feedRepository */
        $feedRepository = $this->getRepository(Feed::class);
        $this->assertCount(0, $feedRepository->findAllExceptByUrls(['https://www.archlinux.de/']));
    }
}
