<?php

namespace App\Common\Infrastructure\Messenger\EventBus;

interface EventBus
{
    public function dispatch(object $event): void;
}
