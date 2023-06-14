<?php

namespace App\Common\Infrastructure\Messenger\CommandBus;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AsCommandHandler
{
}
