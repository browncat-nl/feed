<?php

namespace App\Common\Infrastructure\Messenger\CommandBus;

interface CommandBus
{
    public function handle(object $command): void;
}
