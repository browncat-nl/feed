<?php

namespace App\Feed\Application\Command\Source\Handler;

use App\Common\Domain\Url\Url;
use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Feed\Application\Command\Source\AddSourceCommand;
use App\Feed\Domain\Category\CategoryRepository;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;

#[AsCommandHandler]
final readonly class AddSourceHandler
{
    public function __construct(private SourceRepository $sourceRepository, private CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(AddSourceCommand $command): void
    {
        $source = new Source(
            new SourceId($command->sourceId),
            $command->name,
            Url::createFromString($command->feedUrl),
            $this->categoryRepository->findByNameOrThrow($command->category),
        );

        $this->sourceRepository->save($source);
    }
}
