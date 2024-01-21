<?php

namespace App\Feed\Application\Event\Article;

final readonly class ArticleAddedEvent
{
    public function __construct(
        public string $articleId,
    ) {
    }
}
