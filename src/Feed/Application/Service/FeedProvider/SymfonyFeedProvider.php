<?php

namespace App\Feed\Application\Service\FeedProvider;

use App\Feed\Application\FeedParser\FeedParser;

final class SymfonyFeedProvider implements FeedProvider
{
    public function __construct(
        private FeedParser $feedParser,
    ) {
    }

    public static function getSource(): string
    {
        return 'symfony.com';
    }

    public function fetchFeedItems(): array
    {
        return $this->feedParser->fetchFeed(self::getSource(), 'https://feeds.feedburner.com/symfony/blog');
    }
}
