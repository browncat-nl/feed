<?php

namespace App\Feed\Infrastructure\Console;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Service\FeedProvider\FeedProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsCommand('feed:fetch')]
class FetchExternalFeedsCLICommand extends Command
{
    /**
     * @param iterable<FeedProvider> $feedProviders
     */
    public function __construct(
        #[TaggedIterator(FeedProvider::class)]
        private iterable $feedProviders,
        private CommandBus $commandBus,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $feedItems = [];

        foreach ($this->feedProviders as $feedProvider) {
            $this->logger->info('[feed:fetch] Fetching feed for source {source}', [
                'source' => $feedProvider::getSource(),
            ]);

            $this->commandBus->handle(new FetchFeedCommand(
                $feedProvider::getSource(),
            ));
        }

        $this->logger->info('[feed:fetch] Finished syncing feeds with our datastore');

        return Command::SUCCESS;
    }
}
