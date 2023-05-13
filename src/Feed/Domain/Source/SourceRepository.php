<?php

namespace App\Feed\Domain\Source;

interface SourceRepository
{
    public function save(Source ...$sources): void;

    public function find(SourceId $id): ?Source;
}
