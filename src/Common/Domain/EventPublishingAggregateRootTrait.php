<?php

namespace App\Common\Domain;

trait EventPublishingAggregateRootTrait
{
    /**
     * @var list<object>
     */
    private array $domainEvents = [];

    public function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function shiftDomainEvent(): object
    {
        return array_shift($this->domainEvents) ?? throw new \LogicException('There are no domain events (left).');
    }

    public function hasDomainEvents(): bool
    {
        return $this->domainEvents !== [];
    }
}
