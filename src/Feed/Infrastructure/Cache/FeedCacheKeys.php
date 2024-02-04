<?php

namespace App\Feed\Infrastructure\Cache;

enum FeedCacheKeys : string
{
    case TOTAL_ARTICLES_COUNT = 'total_articles_count';
    case ARTICLE_WITH_ARTICLE_ID = 'article_with_article_id_%s';
}
