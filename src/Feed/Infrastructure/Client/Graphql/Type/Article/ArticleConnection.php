<?php

namespace App\Feed\Infrastructure\Client\Graphql\Type\Article;

use Overblog\GraphQLBundle\Annotation as GraphQL;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Overblog\GraphQLBundle\Relay\Connection\PageInfoInterface;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;

#[GraphQL\Relay\Connection(node: 'Article')]
final class ArticleConnection extends Connection
{
}
