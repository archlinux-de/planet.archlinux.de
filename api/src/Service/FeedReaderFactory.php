<?php

namespace App\Service;

class FeedReaderFactory
{
    /**
     * @param string $feedUrl
     * @return \SimplePie
     */
    public function createFeedReader(string $feedUrl): \SimplePie
    {
        $feedReader = new \SimplePie();
        $feedReader->set_feed_url($feedUrl);
        $feedReader->enable_cache(false);
        $feedReader->enable_exceptions(true);
        $feedReader->init();

        return $feedReader;
    }
}
