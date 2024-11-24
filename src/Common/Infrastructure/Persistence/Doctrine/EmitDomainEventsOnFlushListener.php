<?php

namespace App\Common\Infrastructure\Persistence\Doctrine;

use App\Common\Domain\EventPublishingAggregateRoot;
use App\Common\Infrastructure\Messenger\EventBus\EventBus;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::onFlush, method: 'onFlush')]
final readonly class EmitDomainEventsOnFlushListener
{
    public function __construct(private EventBus $eventBus)
    {
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        $uow = $event->getObjectManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->emitRecordedEvents($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->emitRecordedEvents($entity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->emitRecordedEvents($entity);
        }

        foreach ($uow->getScheduledCollectionDeletions() as $collection) {
            foreach ($collection as $entity) {
                $this->emitRecordedEvents($entity);
            }
        }

        foreach ($uow->getScheduledCollectionUpdates() as $collection) {
            foreach ($collection as $entity) {
                $this->emitRecordedEvents($entity);
            }
        }
    }

    public function emitRecordedEvents(object $entity): void
    {
        if ($entity instanceof EventPublishingAggregateRoot === false) {
            return;
        }

        while ($entity->hasDomainEvents()) {
            $this->eventBus->dispatch($entity->shiftDomainEvent());
        }
    }
}
