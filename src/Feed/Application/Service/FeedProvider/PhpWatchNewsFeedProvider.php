<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;

final class PhpWatchNewsFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
    ) {
    }


    public static function getSource(): string
    {
        return 'php.watch-news';
    }

    public function fetchFeedItems(): array
    {
        return $this->feedParser->fetchFeed(self::getSource(), 'https://php.watch/feed/news.xml');
    }
}
