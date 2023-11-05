<?php

namespace App\Feed\Infrastructure\Client\Graphql\Query;

use App\Feed\Application\Query\Article\CountArticlesQuery;
use App\Feed\Application\Query\Article\Handler\CountArticlesHandler;
use App\Feed\Application\Query\Article\Handler\LatestUpdatedArticlesHandler;
use App\Feed\Application\Query\Article\LatestUpdatedArticlesQuery;
use App\Feed\Infrastructure\Client\Graphql\Type\Article\ArticleType;
use GraphQL\Executor\Promise\Promise;
use Overblog\GraphQLBundle\Annotation as GraphQL;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

#[GraphQL\Provider]
class GetLatestArticlesQuery
{
    public function __construct(
        private LatestUpdatedArticlesHandler $latestUpdatedArticlesHandler,
        private CountArticlesHandler $countArticlesHandler,
    ) {
    }

    #[GraphQL\Query(
        name: 'getLatestArticles',
        type: 'ArticleConnection',
        args: [
            new GraphQL\Arg(name: 'first', type: 'Int', default: 30),
            new GraphQL\Arg(name: 'after', type: 'String', default: null),
        ]
    )]
    public function __invoke(int $first, ?string $after): Connection|Promise
    {
        $paginator = new Paginator(function ($offset, $limit) {
            $articles = $this->latestUpdatedArticlesHandler->__invoke(new LatestUpdatedArticlesQuery($offset, $limit));

            $articleTypes = [];

            foreach ($articles as $article) {
                $articleTypes[] = ArticleType::createFromArticle($article);
            }

            return $articleTypes;
        });

        return $paginator->auto(
            new Argument(['first' => $first, 'after' => $after]),
            $this->countArticlesHandler->__invoke(new CountArticlesQuery()),
        );
    }
}
