<?php

namespace App\Common\Identifier;

use App\Common\Identifier\Exception\MalformedUuidException;
use Stringable;

abstract class UuidId implements Stringable
{
    public function __construct(
        private readonly string $id
    ) {
        $this->validate($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function validate(string $id): void
    {
        if (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $id)) {
            return;
        }

        throw MalformedUuidException::withUuid($id);
    }
}
