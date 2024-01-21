<?php

namespace App\Feed\Application\Event\Article;

final readonly class ArticleUpdatedEvent
{
    public function __construct(
        public string $articleId,
    ) {
    }
}
