<?php

namespace App\Feed\Infrastructure\Client\Graphql\Query;

use App\Feed\Application\Query\Article\Handler\LatestUpdatedArticlesHandler;
use App\Feed\Application\Query\Article\LatestUpdatedArticlesQuery;
use App\Feed\Infrastructure\Client\Graphql\Type\Article\ArticleType;
use Overblog\GraphQLBundle\Annotation as GraphQL;

#[GraphQL\Provider]
class GetLatestArticlesQuery
{
    public function __construct(private LatestUpdatedArticlesHandler $handler)
    {
    }

    /**
     * @return list<ArticleType>
     */
    #[GraphQL\Query(name: 'getLatestArticles', type: '[Article]')]
    public function __invoke(): array
    {
        // @todo number of articles is fixed for now, will have to be replaced by pagination at some point.
        $articles = $this->handler->__invoke(new LatestUpdatedArticlesQuery(30));

        $articleTypes = [];

        foreach ($articles as $article) {
            $articleTypes[] = ArticleType::createFromArticle($article);
        }

        return $articleTypes;
    }
}
