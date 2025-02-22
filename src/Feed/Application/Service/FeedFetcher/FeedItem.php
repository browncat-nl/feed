<?php

namespace App\Feed\Application\Service\FeedFetcher;

use DateTime;

final readonly class FeedItem
{
    public function __construct(
        public string $title,
        public string $summary,
        public string $url,
        public DateTime $updated,
        public string $source,
    ) {
    }
}
