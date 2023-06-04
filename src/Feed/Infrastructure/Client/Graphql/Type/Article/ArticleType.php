<?php

namespace App\Feed\Infrastructure\Client\Graphql\Type\Article;

use App\Feed\Domain\Article\Article;
use DateTime;
use Overblog\GraphQLBundle\Annotation as GraphQL;

#[GraphQL\Type(name: 'Article')]
final class ArticleType
{
    public function __construct(
        #[GraphQL\Field]
        public string $title,
        #[GraphQL\Field]
        public string $summary,
        #[GraphQL\Field]
        public string $url,
    ) {
    }

    public static function createFromArticle(Article $article): self
    {
        return new self(
            $article->getTitle(),
            $article->getSummary(),
            (string) $article->getUrl(),
        );
    }
}
