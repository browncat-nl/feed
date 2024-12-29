<?php

namespace App\Feed\Application\Query\Source\Handler;

use App\Feed\Application\Query\Source\GetAllSourceIdsQuery;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;

final class GetAllSourceIdsHandler
{
    public function __construct(private SourceRepository $sourceRepository)
    {
    }

    /**
     * @param GetAllSourceIdsQuery $query
     * @return list<string>
     */
    public function __invoke(GetAllSourceIdsQuery $query): array
    {
        return array_map(fn($sourceId) => (string) $sourceId, $this->sourceRepository->findAllIds());
    }
}
