<?php

namespace App\Feed\Domain\Article\Event\Article;

final readonly class ArticleAddedEvent
{
    public function __construct(
        public string $articleId,
    ) {
    }
}
