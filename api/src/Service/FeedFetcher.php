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
    /**
     * @param string[] $feedUrls
     */
    public function __construct(private array $feedUrls, private FeedReaderFactory $feedReaderFactory)
    {
    }

    /**
     * @return string[]
     */
    public function getFeedUrls(): array
    {
        return $this->feedUrls;
    }

    /**
     * @return \Traversable<Feed>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->feedUrls as $feedUrl) {
            $feedReader = $this->feedReaderFactory->createFeedReader($feedUrl);
            $feed = $this->createFeed($feedReader);

            if ($feedReader->get_items() !== null) {
                /** @var \SimplePie_Item $feedReaderItem */
                foreach ($feedReader->get_items() as $feedReaderItem) {
                    $feed->addItem($this->createItem($feedReaderItem));
                }
            }
            yield $feed;
        }
    }

    private function createFeed(\SimplePie $feedReader): Feed
    {
        // @FIXME: Sanitize headers that might not contain actual URLs but preload hints
        unset($feedReader->data['headers']['link']);

        return (new Feed($feedReader->feed_url))
            ->setDescription($feedReader->get_description())
            ->setLastModified(
                new \DateTime(
                    $feedReader->get_item() !== null
                        ? (string)$feedReader->get_item()->get_date()
                        : 'now'
                )
            )
            ->setLink($feedReader->get_link() ?? '')
            ->setTitle($feedReader->get_title() ?? '');
    }

    private function createItem(\SimplePie_Item $feedReaderItem): Item
    {
        return (new Item((string)$feedReaderItem->get_link()))
            ->setLastModified(new \DateTime((string)$feedReaderItem->get_date()))
            ->setTitle(html_entity_decode($feedReaderItem->get_title() ?? ''))
            ->setDescription($feedReaderItem->get_description() ?? '')
            ->setAuthor($this->createAuthor($feedReaderItem));
    }

    private function createAuthor(\SimplePie_Item $feedReaderItem): Author
    {
        $author = new Author();
        if ($feedReaderItem->get_author() !== null) {
            $author
                ->setUri($feedReaderItem->get_author()->get_link())
                ->setName($feedReaderItem->get_author()->get_name());
        }
        return $author;
    }
}
