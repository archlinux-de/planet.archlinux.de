<?php

namespace App\Service;

use FeedIo\Feed\NodeInterface;
use FeedIo\FeedInterface;
use FeedIo\Reader\FixerAbstract;

class FeedLinkFixer extends FixerAbstract
{
    /**
     * @param  FeedInterface $feed
     * @return $this
     */
    public function correct(FeedInterface $feed): FixerAbstract
    {
        $feedUrl = $feed->getUrl();

        $parsedFeedUrl = parse_url($feedUrl);
        $absoluteUrl = $parsedFeedUrl['scheme'] . '://' . $parsedFeedUrl['host'];
        if (!empty($parsedFeedUrl['port'])) {
            $absoluteUrl .= ':' . $parsedFeedUrl['port'];
        }
        $this->fixNode($feed, $absoluteUrl);
        $this->fixItems($feed, $absoluteUrl);

        return $this;
    }

    /**
     * @param NodeInterface $node
     * @param string $absoluteUrl
     */
    protected function fixNode(NodeInterface $node, string $absoluteUrl): void
    {
        if (strpos($node->getLink(), '/') === 0) {
            $this->logger->notice("correct link for node {$node->getTitle()}");
            $node->setLink($absoluteUrl . $node->getLink());
        }
    }

    /**
     * @param FeedInterface $feed
     * @param string $absoluteUrl
     */
    protected function fixItems(FeedInterface $feed, string $absoluteUrl): void
    {
        foreach ($feed as $item) {
            $this->fixNode($item, $absoluteUrl);
        }
    }
}
