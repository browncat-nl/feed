<?php

namespace App\Feed\Application\Event\Feed;

use App\Feed\Application\FeedParser\FeedItem;

final readonly class FeedItemWasFetchedEvent
{
    public function __construct(
        public FeedItem $feedItem,
    ) {
    }
}
