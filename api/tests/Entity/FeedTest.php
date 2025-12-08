<?php

namespace App\Tests\Entity;

use App\Entity\Feed;
use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class FeedTest extends TestCase
{
    public function testEntity(): void
    {
        $item = $this->createStub(Item::class);
        $feed = new Feed('https://www.archlinux.de/news/feed')
            ->setDescription('Arch Linux News')
            ->setLink('https://www.archlinux.de/news')
            ->setTitle('Arch Linux')
            ->setLastModified(new \DateTime('2019-01-01'))
            ->addItem($item);

        $this->assertEquals('https://www.archlinux.de/news/feed', $feed->getUrl());
        $this->assertEquals('Arch Linux News', $feed->getDescription());
        $this->assertEquals('https://www.archlinux.de/news', $feed->getLink());
        $this->assertEquals('Arch Linux', $feed->getTitle());
        $this->assertEquals(new \DateTime('2019-01-01'), $feed->getLastModified());
        $this->assertEquals($item, $feed->getItems()->first());
    }
}
