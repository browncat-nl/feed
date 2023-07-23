<?php

namespace Dev\Common\Infrastructure\Messenger\CommandBus;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;

final class RecordingCommandBus implements CommandBus
{
    /**
     * @var list<object>
     */
    private array $recordedCommands = [];

    public function handle(object $command): void
    {
        $this->recordedCommands[] = $command;
    }

    public function shiftCommand(): object
    {
        return array_shift($this->recordedCommands) ?? throw new \LogicException('There are no recorded commands (left).');
    }

    public function isEmpty(): bool
    {
        return $this->recordedCommands === [];
    }
}
