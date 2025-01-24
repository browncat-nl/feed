<?php

namespace App\Feed\Application\Model\Source;

use App\Feed\Domain\Source\Source;

final readonly class SourceReadModel
{
    private function __construct(
        public string $id,
        public string $name,
        public string $category,
    ) {
    }

    public static function fromSource(Source $source): self
    {
        return new self(
            $source->getId(),
            $source->getName(),
            $source->getCategory()->getName(),
        );
    }
}
