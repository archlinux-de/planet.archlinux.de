<?php

namespace App\Tests\Entity;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testEntity()
    {
        /** @var Author|MockObject $author */
        $author = $this->createMock(Author::class);
        /** @var Feed|MockObject $feed */
        $feed = $this->createMock(Feed::class);
        $item = (new Item())
            ->setLastModified(new \DateTime('2019-01-01'))
            ->setTitle('Item Title')
            ->setLink('https://www.archlinux.de/news/item')
            ->setDescription('Item Description')
            ->setPublicId('1')
            ->setAuthor($author)
            ->setFeed($feed);

        $this->assertEquals(new \DateTime('2019-01-01'), $item->getLastModified());
        $this->assertEquals('Item Title', $item->getTitle());
        $this->assertEquals('https://www.archlinux.de/news/item', $item->getLink());
        $this->assertEquals('Item Description', $item->getDescription());
        $this->assertEquals('1', $item->getPublicId());
        $this->assertEquals($author, $item->getAuthor());
        $this->assertEquals($feed, $item->getFeed());
    }
}
