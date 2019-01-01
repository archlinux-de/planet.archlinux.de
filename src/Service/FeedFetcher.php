<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Feed;
use App\Entity\Item;
use FeedIo\Feed\ItemInterface;
use FeedIo\FeedInterface;
use FeedIo\FeedIo;

class FeedFetcher implements \IteratorAggregate
{
    /** @var FeedIo */
    private $feedIo;

    /** @var string */
    private $feedUrls;

    /**
     * @param FeedIo $feedIo
     * @param array $feedUrls
     */
    public function __construct(FeedIo $feedIo, array $feedUrls)
    {
        $this->feedIo = $feedIo;
        $this->feedUrls = $feedUrls;
    }

    /**
     * @return iterable
     */
    public function getIterator(): iterable
    {
        foreach ($this->feedUrls as $feedUrl) {
            $feedReader = $this->fetchFeed($feedUrl);
            $feed = new Feed($feedReader->getUrl());
            $feed
                ->setDescription($feedReader->getDescription())
                ->setLastModified($feedReader->getLastModified())
                ->setLink($feedReader->getLink())
                ->setTitle($feedReader->getTitle());

            /** @var ItemInterface $feedReaderItem */
            foreach ($feedReader as $feedReaderItem) {
                $item = new Item();
                $item
                    ->setPublicId($feedReaderItem->getPublicId())
                    ->setLastModified($feedReaderItem->getLastModified())
                    ->setTitle($feedReaderItem->getTitle())
                    ->setLink($feedReaderItem->getLink())
                    ->setDescription($feedReaderItem->getDescription())
                    ->setLastModified($feedReaderItem->getLastModified());
                if (!is_null($feedReaderItem->getAuthor())) {
                    $item->setAuthor(
                        (new Author())
                            ->setUri($feedReaderItem->getAuthor()->getUri())
                            ->setName($feedReaderItem->getAuthor()->getName())
                    );
                }
                $feed->addItem($item);
            }
            yield $feed;
        }
    }

    /**
     * @param string $feedUrl
     * @return FeedInterface
     */
    private function fetchFeed(string $feedUrl): FeedInterface
    {
        $feedReader = $this->feedIo->read(
            $feedUrl,
            (new \FeedIo\Feed())->setUrl($feedUrl)
        )->getFeed();
        if ($feedReader->count() == 0) {
            throw new \RuntimeException(sprintf('empty feed "%s"', $feedUrl));
        }
        return $feedReader;
    }
}
