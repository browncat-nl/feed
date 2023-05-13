<?php

namespace App\Feed\Domain\Source;

use Stringable;

final readonly class SourceId implements Stringable
{
    public function __construct(private string $id)
    {
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
