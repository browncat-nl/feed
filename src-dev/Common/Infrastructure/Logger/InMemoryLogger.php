<?php

namespace Dev\Common\Infrastructure\Logger;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class InMemoryLogger implements LoggerInterface
{
    /**
     * @var list<RecordedLog>
     */
    public array $recordedLogs = [];

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::EMERGENCY,
            created: new DateTimeImmutable(),
        );
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::ALERT,
            created: new DateTimeImmutable(),
        );
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::CRITICAL,
            created: new DateTimeImmutable(),
        );
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::ERROR,
            created: new DateTimeImmutable(),
        );
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::WARNING,
            created: new DateTimeImmutable(),
        );
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::NOTICE,
            created: new DateTimeImmutable(),
        );
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::INFO,
            created: new DateTimeImmutable(),
        );
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: LogLevel::DEBUG,
            created: new DateTimeImmutable(),
        );
    }

    /**
     * @param \Stringable|string $level
     * @param \Stringable|string $message
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->recordedLogs[] = new RecordedLog(
            message: $message,
            context: $context,
            level: $level,
            created: new DateTimeImmutable(),
        );
    }
}
