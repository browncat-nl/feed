<?php

namespace App\Feed\Infrastructure\Client\Graphql\Type\Article;

use App\Common\Infrastructure\Client\Graphql\Scalar\DateTimeType;
use App\Feed\Application\Model\Article\ArticleReadModel;
use App\Feed\Infrastructure\Client\Graphql\Type\Source\SourceType;
use DateTimeImmutable;
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
        #[GraphQL\Field]
        public SourceType $source,
        #[GraphQl\Field(type: "DateTime")]
        public DateTimeImmutable $updated,
    ) {
    }

    public static function createFromArticleReadModel(ArticleReadModel $article): self
    {
        return new self(
            $article->title,
            $article->summary,
            $article->url,
            SourceType::createFromSourceReadModel($article->source),
            $article->updated,
        );
    }
}
