<?php

namespace App\Feed\Application\Service\FeedFetcher;

interface FeedFetcher
{
    /**
     * @return List<FeedItem>
     */
    public function __invoke(string $source, string $url): array;
}
