<?php

namespace Unit\Common\Identifier;

use App\Common\Identifier\Exception\MalformedUuidException;
use App\Common\Identifier\UuidId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UuidIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_create_the_uuid_id(): void
    {
        // Arrange
        $uuid = Uuid::uuid4();

        // Act
        $testId = new class ((string) $uuid) extends UuidId {
        };

        // Assert
        self::assertSame((string) $uuid, (string) $testId);
    }

    /**
     * @test
     */
    public function it_should_guard_if_uuid_is_invalid(): void
    {
        // Arrange
        $uuid = 'very-invalid-uuid';

        // Assert
        self::expectExceptionObject(MalformedUuidException::withUuid($uuid));

        // Act
        new class ($uuid) extends UuidId {
        };
    }
}
