<?php

namespace Unit\Feed\Infrastructure\Console;

use App\Feed\Application\Command\Source\AddSourceCommand;
use App\Feed\Infrastructure\Console\AddSourceCLICommand;
use Dev\Common\Infrastructure\Messenger\CommandBus\RecordingCommandBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class AddSourceCLICommandTest extends TestCase
{
    private RecordingCommandBus $commandBus;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = new RecordingCommandBus();
        $this->commandTester = new CommandTester(new AddSourceCLICommand($this->commandBus));
    }

    /**
     * @test
     */
    public function it_should_call_the_add_source_command(): void
    {
        // Act
        $this->commandTester->execute([
            'name' => 'some-source',
            'feedUrl' => 'https://example.com/some-source'
        ]);

        // Assert
        $command = $this->commandBus->shiftCommand();

        self::assertInstanceOf(AddSourceCommand::class, $command);
        self::assertSame('some-source', $command->name);
        self::assertSame('https://example.com/some-source', $command->feedUrl);
    }
}
