<?php

namespace App\Common\Infrastructure\Client\Graphql\Scalar;

use DateTime;
use DateTimeImmutable;
use GraphQL\Language\AST\StringValueNode;
use Overblog\GraphQLBundle\Annotation as GraphQL;

#[GraphQL\Scalar(name: "DateTime")]
final readonly class DateTimeType
{
    public static function serialize(DateTime|DateTimeImmutable $value): string
    {
        return $value->format('Y-m-d H:i:s');
    }

    public static function parseValue(string $value): DateTime
    {
        return new DateTime($value);
    }

    public static function parseLiteral(StringValueNode $valueNode): DateTime
    {
        return new DateTime($valueNode->value);
    }
}
