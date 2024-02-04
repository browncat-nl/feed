<?php

namespace App\Feed\Application\Model\Article;

use App\Feed\Application\Model\Source\SourceReadModel;
use App\Feed\Domain\Article\Article;
use DateTimeImmutable;

final readonly class ArticleReadModel
{
    private function __construct(
        public string $title,
        public string $summary,
        public string $url,
        public SourceReadModel $source,
        public DateTimeImmutable $updated,
    ) {
    }

    public static function fromArticle(Article $article): self
    {
        return new self(
            $article->getTitle(),
            $article->getSummary(),
            (string) $article->getUrl(),
            SourceReadModel::fromSource($article->getSource()),
            DateTimeImmutable::createFromMutable($article->getUpdated()),
        );
    }
}
