<?php

namespace App\Service;

class FeedReaderFactory
{
    public function createFeedReader(string $feedUrl): \SimplePie
    {
        $feedReader = new \SimplePie();
        $feedReader->set_feed_url($feedUrl);
        $feedReader->enable_cache(false);
        $feedReader->enable_exceptions(true);
        $feedReader->set_useragent('planet.archlinux.de/1.0');
        $feedReader->init();

        return $feedReader;
    }
}
