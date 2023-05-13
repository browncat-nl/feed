<?php

namespace Dev\Feed\Repository;

use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;

final class InMemorySourceRepository implements SourceRepository
{
    /**
     * @var array<string, Source>
     */
    private array $entities;

    public function save(Source ...$sources): void
    {
        foreach ($sources as $source) {
            $this->entities[(string) $source->id] = $source;
        }
    }

    public function find(SourceId $id): ?Source
    {
        return $this->entities[(string) $id] ?? null;
    }
}
