<?php

namespace App\Feed\Infrastructure\Persistence\Doctrine\Source;

use App\Common\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends DoctrineRepository<Source>
 */
final readonly class DoctrineSourceRepository extends DoctrineRepository implements SourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function save(Source ...$articles): void
    {
        $this->saveWithoutTransaction(...$articles);
    }

    public function find(SourceId $id): ?Source
    {
        return $this->findWithoutLocking((string) $id);
    }

    public function findOrThrow(SourceId $id): Source
    {
        return $this->find($id) ?? throw SourceNotFoundException::withSourceId($id);
    }
}
