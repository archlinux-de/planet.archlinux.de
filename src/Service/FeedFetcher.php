<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;

/**
 * @phpstan-implements \IteratorAggregate<Feed>
 */
class FeedFetcher implements \IteratorAggregate
{
    /** @var string[] */
    private $feedUrls;

    /** @var FeedReaderFactory */
    private $feedReaderFactory;

    /**
     * @param string[] $feedUrls
     * @param FeedReaderFactory $feedReaderFactory
     */
    public function __construct(array $feedUrls, FeedReaderFactory $feedReaderFactory)
    {
        $this->feedUrls = $feedUrls;
        $this->feedReaderFactory = $feedReaderFactory;
    }

    /**
     * @return string[]
     */
    public function getFeedUrls(): array
    {
        return $this->feedUrls;
    }

    /**
     * @return iterable<Feed>
     */
    public function getIterator(): iterable
    {
        foreach ($this->feedUrls as $feedUrl) {
            $feedReader = $this->feedReaderFactory->createFeedReader($feedUrl);
            $feed = $this->createFeed($feedReader);

            if (!is_null($feedReader->get_items())) {
                /** @var \SimplePie_Item $feedReaderItem */
                foreach ($feedReader->get_items() as $feedReaderItem) {
                    $feed->addItem($this->createItem($feedReaderItem));
                }
            }
            yield $feed;
        }
    }

    /**
     * @param \SimplePie $feedReader
     * @return Feed
     */
    private function createFeed(\SimplePie $feedReader): Feed
    {
        return (new Feed($feedReader->feed_url))
            ->setDescription($feedReader->get_description())
            ->setLastModified(
                new \DateTime(
                    !is_null($feedReader->get_item())
                        ? (string)$feedReader->get_item()->get_date()
                        : 'now'
                )
            )
            ->setLink($feedReader->get_link() ?? '')
            ->setTitle($feedReader->get_title() ?? '');
    }

    /**
     * @param \SimplePie_Item $feedReaderItem
     * @return Item
     */
    private function createItem(\SimplePie_Item $feedReaderItem): Item
    {
        return (new Item((string)$feedReaderItem->get_link()))
            ->setLastModified(new \DateTime((string)$feedReaderItem->get_date()))
            ->setTitle(html_entity_decode($feedReaderItem->get_title() ?? ''))
            ->setDescription($feedReaderItem->get_description() ?? '')
            ->setAuthor($this->createAuthor($feedReaderItem));
    }

    /**
     * @param \SimplePie_Item $feedReaderItem
     * @return Author
     */
    private function createAuthor(\SimplePie_Item $feedReaderItem): Author
    {
        $author = new Author();
        if (!is_null($feedReaderItem->get_author())) {
            $author
                ->setUri($feedReaderItem->get_author()->get_link())
                ->setName($feedReaderItem->get_author()->get_name());
        }
        return $author;
    }
}
