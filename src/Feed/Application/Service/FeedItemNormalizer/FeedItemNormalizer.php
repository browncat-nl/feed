<?php

namespace App\Feed\Application\Service\FeedItemNormalizer;

use App\Feed\Application\FeedParser\FeedItem;

interface FeedItemNormalizer
{
    public function __invoke(FeedItem $feedItem): FeedItem;
}
