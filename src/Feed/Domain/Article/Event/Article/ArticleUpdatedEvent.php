<?php

namespace App\Feed\Domain\Article\Event\Article;

final readonly class ArticleUpdatedEvent
{
    public function __construct(
        public string $articleId,
    ) {
    }
}
