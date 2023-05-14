<?php

namespace App\Feed\Domain\Source;

use App\Feed\Domain\Source\Exception\SourceNotFoundException;

interface SourceRepository
{
    public function save(Source ...$sources): void;

    public function find(SourceId $id): ?Source;

    /**
     * @throws SourceNotFoundException
     */
    public function findOrThrow(SourceId $id): Source;
}
