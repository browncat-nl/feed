<?php

namespace App\Feed\Application\FeedParser;

interface FeedParser
{
    /**
     * @return List<FeedItem>
     */
    public function fetchFeed(string $source, string $url): array;
}
