<?php

namespace Dev\Feed\Repository;

use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;

final class InMemorySourceRepository implements SourceRepository
{
    /**
     * @var array<string, Source>
     */
    private array $entities = [];

    public function save(Source ...$sources): void
    {
        foreach ($sources as $source) {
            $this->entities[(string) $source->getId()] = $source;
        }
    }

    public function find(SourceId $id): ?Source
    {
        return $this->entities[(string) $id] ?? null;
    }

    public function findOrThrow(SourceId $id): Source
    {
        return $this->find($id) ?? throw SourceNotFoundException::withSourceId($id);
    }

    public function findByNameOrThrow(string $name): Source
    {
        foreach ($this->entities as $entity) {
            if ($entity->getName() !== $name) {
                continue;
            }

            return $entity;
        }

        throw SourceNotFoundException::withName($name);
    }

    public function findAllIds(): array
    {
        return array_values(array_map(fn ($source) => $source->getId(), $this->entities));
    }
}
