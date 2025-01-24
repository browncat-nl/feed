<?php

namespace App\Feed\Infrastructure\Console;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Feed\Application\Command\Source\AddSourceCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('source:add')]
final class AddSourceCLICommand extends Command
{
    public function __construct(private CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('feedUrl', InputArgument::REQUIRED)
            ->addArgument('category', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->handle(new AddSourceCommand(
            Uuid::uuid4(),
            $input->getArgument('name'),
            $input->getArgument('feedUrl'),
            $input->getArgument('category'),
        ));

        return Command::SUCCESS;
    }
}
