<?php

namespace App\Tests\Repository;

use App\Entity\Feed;
use App\Entity\Item;
use App\Repository\ItemRepository;
use SymfonyDatabaseTest\DatabaseTestCase;

class ItemRepositoryTest extends DatabaseTestCase
{
    public function testFindLatest(): void
    {
        $feed = new Feed('https://www.archlinux.de/')
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = new Item('https://www.archlinux.de/news/1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setFeed($feed);
        $newItem = new Item('https://www.archlinux.de/news/2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items = iterator_to_array($itemRepository->findLatest(0, 2));
        $this->assertCount(2, $items);
        $this->assertEquals($newItem->getLink(), $items[0]->getLink());
        $this->assertEquals($oldItem->getLink(), $items[1]->getLink());
    }

    public function testFindLatestItemsAreLimited(): void
    {
        $feed = new Feed('https://www.archlinux.de/')
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = new Item('https://www.archlinux.de/news/1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setFeed($feed);
        $newItem = new Item('https://www.archlinux.de/news/2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items =  iterator_to_array($itemRepository->findLatest(0, 1));
        $this->assertCount(1, $items);
        $this->assertEquals($newItem->getLink(), $items[0]->getLink());
    }
}
