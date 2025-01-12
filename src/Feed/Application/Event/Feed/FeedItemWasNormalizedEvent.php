<?php

namespace App\Feed\Application\Event\Feed;

use App\Feed\Application\Service\FeedFetcher\FeedItem;

final readonly class FeedItemWasNormalizedEvent
{
    public function __construct(
        public FeedItem $feedItem,
    ) {
    }
}
