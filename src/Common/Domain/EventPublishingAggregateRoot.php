<?php

namespace App\Common\Domain;

interface EventPublishingAggregateRoot
{
    public function recordEvent(object $event): void;

    public function shiftDomainEvent(): object;

    public function hasDomainEvents(): bool;
}
