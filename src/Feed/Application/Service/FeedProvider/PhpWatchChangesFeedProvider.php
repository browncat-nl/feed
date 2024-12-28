<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;

final class PhpWatchChangesFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
    ) {
    }


    public static function getSource(): string
    {
        return 'php.watch-changes';
    }

    public function fetchFeedItems(): array
    {
        return $this->feedParser->fetchFeed($this::getSource(), 'https://php.watch/feed/php-changes.xml');
    }
}
