<?php

namespace App\Common\Infrastructure\Messenger\EventBus;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AsEventSubscriber
{
}
