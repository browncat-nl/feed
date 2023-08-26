<?php

namespace App\Feed\Infrastructure\Client\Graphql\Type\Source;

use App\Feed\Domain\Source\Source;
use Overblog\GraphQLBundle\Annotation as GraphQL;

#[GraphQL\Type(name: 'Source')]
final readonly class SourceType
{
    public function __construct(
        #[GraphQL\Field]
        public string $id,
        #[GraphQL\Field]
        public string $name,
    ) {
    }

    public static function createFromSource(Source $source): self
    {
        return new self(
            $source->getId(),
            $source->getName(),
        );
    }
}
