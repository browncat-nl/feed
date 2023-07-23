<?php

namespace App\Feed\Application\Service\FeedProvider;

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
