<?php

namespace App\Tests\Service;

use App\Entity\Feed;
use App\Entity\Item;
use App\Service\FeedFetcher;
use App\Service\FeedReaderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimplePie\SimplePie;

class FeedFetcherTest extends TestCase
{
    public function testGetIterator(): void
    {
        $feedReader = new SimplePie();
        $feedReader->set_raw_data(
            '<?xml version="1.0" encoding="utf-8"?>
                   <feed xmlns="http://www.w3.org/2005/Atom">

                     <title>Example Feed</title>
                     <link href="http://example.org/"/>
                     <updated>2003-12-13T18:30:02Z</updated>
                     <author>
                       <name>John Doe</name>
                     </author>
                     <id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>

                     <entry>
                       <title>Atom-Powered Robots Run Amok</title>
                       <link href="http://example.org/2003/12/13/atom03"/>
                       <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
                       <updated>2003-12-13T18:30:02Z</updated>
                       <summary>Some text.</summary>
                     </entry>

                   </feed>'
        );
        $feedReader->enable_cache(false);
        $feedReader->enable_exceptions(true);
        $feedReader->init();
        /** Init reader before so URL is not actually used */
        $feedReader->set_feed_url('');

        /** @var FeedReaderFactory|MockObject $feedReaderFactory */
        $feedReaderFactory = $this->createMock(FeedReaderFactory::class);
        $feedReaderFactory
            ->expects($this->once())
            ->method('createFeedReader')
            ->with('http://example.org/')
            ->willReturn($feedReader);

        $feedFetcher = new FeedFetcher(
            ['http://example.org/'],
            $feedReaderFactory
        );

        $feeds = iterator_to_array($feedFetcher);

        $this->assertCount(1, $feeds);
        /** @var Feed $feed */
        $feed = $feeds[0];
        $this->assertEquals('http://example.org/', $feed->getLink());
        $this->assertEquals('Example Feed', $feed->getTitle());

        $this->assertEquals(1, $feed->getItems()->count());
        /** @var Item $item */
        $item = $feed->getItems()->first();
        $this->assertEquals('Atom-Powered Robots Run Amok', $item->getTitle());
        $this->assertEquals('http://example.org/2003/12/13/atom03', $item->getLink());
        $this->assertEquals('Some text.', $item->getDescription());
    }

    public function testGetFeedUrls(): void
    {
        $feedReaderFactory = $this->createStub(FeedReaderFactory::class);
        $feedFetcher = new FeedFetcher(
            ['https://www.archlinux.de/news/feed'],
            $feedReaderFactory
        );

        $this->assertEquals(['https://www.archlinux.de/news/feed'], $feedFetcher->getFeedUrls());
    }
}
