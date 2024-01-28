<?php

namespace App\Feed\Infrastructure\Cache;

enum FeedCacheKeys : string
{
    case TOTAL_ARTICLES_COUNT = 'total_articles_count';
}
