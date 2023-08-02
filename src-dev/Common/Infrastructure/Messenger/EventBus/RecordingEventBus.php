<?php

namespace Dev\Common\Infrastructure\Messenger\EventBus;

use App\Common\Infrastructure\Messenger\EventBus\EventBus;

final class RecordingEventBus implements EventBus
{
    /**
     * @var list<object>
     */
    private array $recordedEvents = [];

    public function dispatch(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function shiftEvent(): object
    {
        return array_shift($this->recordedEvents) ?? throw new \LogicException('There are no recorded events (left).');
    }

    public function isEmpty(): bool
    {
        return $this->recordedEvents === [];
    }
}
