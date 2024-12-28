<?php

namespace App\Feed\Application\FeedParser;

use App\Feed\Application\Service\FeedProvider\FeedItem;

interface FeedParser
{
    /**
     * @return List<FeedItem>
     */
    public function fetchFeed(string $source, string $url): array;
}
