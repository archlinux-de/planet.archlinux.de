<?php

namespace App\Tests\Repository;

use App\Entity\Feed;
use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Tests\Util\DatabaseTestCase;

class ItemRepositoryTest extends DatabaseTestCase
{
    public function testFindAllExceptByIdsIfNoIdsAreGiven()
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $item = (new Item())
            ->setPublicId('1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($item);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items = $itemRepository->findAllExceptByIds($feed, []);
        $this->assertCount(1, $items);
        $this->assertEquals($item->getPublicId(), $items[0]->getPublicId());
    }

    public function testFindAllExceptByIds()
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = (new Item())
            ->setPublicId('1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);
        $newItem = (new Item())
            ->setPublicId('2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items = $itemRepository->findAllExceptByIds($feed, ['2']);
        $this->assertCount(1, $items);
        $this->assertEquals($oldItem->getPublicId(), $items[0]->getPublicId());
    }

    public function testFindLatest()
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = (new Item())
            ->setPublicId('1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);
        $newItem = (new Item())
            ->setPublicId('2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items = $itemRepository->findLatest(2);
        $this->assertCount(2, $items);
        $this->assertEquals($newItem->getPublicId(), $items[0]->getPublicId());
        $this->assertEquals($oldItem->getPublicId(), $items[1]->getPublicId());
    }

    public function testFindLatestItemsAreLimited()
    {
        $feed = (new Feed('https://www.archlinux.de/'))
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime())
            ->setLink('https://www.archlinux.de/news/feed');
        $oldItem = (new Item())
            ->setPublicId('1')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('- 2 day'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);
        $newItem = (new Item())
            ->setPublicId('2')
            ->setTitle('Item Title')
            ->setDescription('Item Description')
            ->setLastModified(new \DateTime('now'))
            ->setLink('https://www.archlinux.de/news/item')
            ->setFeed($feed);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($oldItem);
        $entityManager->persist($newItem);
        $entityManager->flush();
        $entityManager->clear();

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getRepository(Item::class);
        $items = $itemRepository->findLatest(1);
        $this->assertCount(1, $items);
        $this->assertEquals($newItem->getPublicId(), $items[0]->getPublicId());
    }
}
