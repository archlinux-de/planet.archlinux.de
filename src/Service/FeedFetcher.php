<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;

class FeedFetcher implements \IteratorAggregate
{
    /** @var array */
    private $feedUrls;

    /**
     * @param array $feedUrls
     */
    public function __construct(array $feedUrls)
    {
        $this->feedUrls = $feedUrls;
    }

    /**
     * @return array
     */
    public function getFeedUrls(): array
    {
        return $this->feedUrls;
    }

    /**
     * @return iterable
     */
    public function getIterator(): iterable
    {
        foreach ($this->feedUrls as $feedUrl) {
            $feedReader = $this->createFeedReader($feedUrl);
            $feed = $this->createFeed($feedReader);

            /** @var \SimplePie_Item $feedReaderItem */
            foreach ($feedReader->get_items() as $feedReaderItem) {
                $feed->addItem($this->createItem($feedReaderItem));
            }
            yield $feed;
        }
    }

    /**
     * @param string $feedUrl
     *
     * @return \SimplePie
     */
    private function createFeedReader(string $feedUrl): \SimplePie
    {
        $feed = new \SimplePie();
        $feed->set_feed_url($feedUrl);
        $feed->enable_cache(false);
        $feed->enable_exceptions(true);
        $feed->init();

        return $feed;
    }

    /**
     * @param \SimplePie $feedReader
     * @return Feed
     */
    private function createFeed(\SimplePie $feedReader): Feed
    {
        return (new Feed($feedReader->feed_url))
            ->setDescription($feedReader->get_description())
            ->setLastModified($this->createDateTime($feedReader->get_item()->get_date()))
            ->setLink($feedReader->get_link())
            ->setTitle($feedReader->get_title());
    }

    /**
     * @param string $timestamp
     * @return \DateTime
     */
    private function createDateTime(string $timestamp): \DateTime
    {
        try {
            return new \DateTime($timestamp);
        } catch (\Exception $e) {
            return new \DateTime();
        }
    }

    /**
     * @param \SimplePie_Item $feedReaderItem
     * @return Item
     */
    private function createItem(\SimplePie_Item $feedReaderItem): Item
    {
        return (new Item())
            ->setPublicId($feedReaderItem->get_id())
            ->setLastModified($this->createDateTime($feedReaderItem->get_date()))
            ->setTitle($feedReaderItem->get_title())
            ->setLink($feedReaderItem->get_link())
            ->setDescription($feedReaderItem->get_description())
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
