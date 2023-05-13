<?php

namespace App\Feed\Domain\Source;

final readonly class Source
{
    public function __construct(
        public SourceId $id,
        public string $name,
    ) {
    }
}
