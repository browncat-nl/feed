<?php

namespace App\Feed\Application\Event\Feed;

use App\Feed\Application\Service\FeedProvider\FeedItem;
use DateTime;

final readonly class FeedItemWasFetchedEvent
{
    public function __construct(
        public FeedItem $feedItem,
    ) {
    }
}
