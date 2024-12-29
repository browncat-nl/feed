<?php

namespace App\Feed\Infrastructure\Console;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Query\Source\GetAllSourceIdsQuery;
use App\Feed\Application\Query\Source\Handler\GetAllSourceIdsHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('feed:fetch')]
class FetchExternalFeedsCLICommand extends Command
{
    public function __construct(
        private GetAllSourceIdsHandler $getAllSourceIdsHandler,
        private CommandBus $commandBus,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getAllSourceIdsHandler->__invoke(new GetAllSourceIdsQuery()) as $sourceId) {
            $this->logger->info('[feed:fetch] Fetching feed for source {source}', [
                'sourceId' => $sourceId,
            ]);

            $this->commandBus->handle(new FetchFeedCommand(
                $sourceId,
            ));
        }

        $this->logger->info('[feed:fetch] Finished syncing feeds with our datastore');

        return Command::SUCCESS;
    }
}
